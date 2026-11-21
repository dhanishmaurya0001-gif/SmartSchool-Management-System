param(
    [string]$XamppPath = 'C:\xampp'
)

$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath $PSScriptRoot
$php = Join-Path $XamppPath 'php\php.exe'
$mysql = Join-Path $XamppPath 'mysql\bin\mysql.exe'
$server = Join-Path $XamppPath 'mysql\bin\mysqld.exe'
$installer = Join-Path $XamppPath 'mysql\bin\mysql_install_db.exe'
foreach ($binary in $php, $mysql, $server, $installer) {
    if (!(Test-Path -LiteralPath $binary)) { throw "XAMPP component missing: $binary" }
}

$runtime = Join-Path $PSScriptRoot 'runtime'
$data = Join-Path $runtime 'mariadb'
$keyFile = Join-Path $runtime 'keys.json'
$readyFile = Join-Path $runtime 'initialized'
$httpPort = 18097
$dbPort = 33318
$url = "http://127.0.0.1:$httpPort"
New-Item -ItemType Directory -Force $runtime | Out-Null

if (!(Test-Path -LiteralPath $keyFile)) {
    @{
        database = [guid]::NewGuid().ToString('N')
        app = [guid]::NewGuid().ToString('N') + [guid]::NewGuid().ToString('N')
    } | ConvertTo-Json | Set-Content -LiteralPath $keyFile -Encoding ascii
}
$keys = Get-Content -LiteralPath $keyFile -Raw | ConvertFrom-Json
$env:SMART_SCHOOL_DB_PASSWORD = $keys.database
$env:SMART_SCHOOL_APP_KEY = $keys.app
$mysqlArgs = @('--no-defaults', '-h', '127.0.0.1', '-P', "$dbPort", '-u', 'root')
$initialized = Test-Path -LiteralPath $readyFile
$dbPidFile = Join-Path $runtime 'mariadb.pid'
$httpPidFile = Join-Path $runtime 'php.pid'

function Get-OwnedListener($port, $pidFile) {
    $listeners = @(Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue)
    if (!$listeners.Count) { return $false }
    if (Test-Path -LiteralPath $pidFile) {
        $owner = [int](Get-Content -LiteralPath $pidFile)
        if ($listeners.Count -eq 1 -and $listeners[0].OwningProcess -eq $owner -and $listeners[0].LocalAddress -eq '127.0.0.1') {
            return $true
        }
    }
    throw "Port $port is occupied by another process. Free that port and run again."
}

if (!(Get-OwnedListener $dbPort $dbPidFile)) {
    if (!(Test-Path -LiteralPath (Join-Path $data 'mysql'))) {
        & $installer "--datadir=$data" "--port=$dbPort"
        if ($LASTEXITCODE) { throw 'MariaDB initialization failed.' }
    }
    $dbLog = Join-Path $runtime 'mariadb-error.log'
    $dbBase = Join-Path $XamppPath 'mysql'
    $daemon = Start-Process $server -ArgumentList "--no-defaults --datadir=`"$data`" --basedir=`"$dbBase`" --bind-address=127.0.0.1 --port=$dbPort --console" -WindowStyle Hidden -PassThru -RedirectStandardError $dbLog -RedirectStandardOutput (Join-Path $runtime 'mariadb.log')
    Set-Content -LiteralPath $dbPidFile -Value $daemon.Id
}

$connected = $false
for ($attempt = 0; $attempt -lt 24; $attempt++) {
    if (Get-OwnedListener $dbPort $dbPidFile) {
        & $mysql @mysqlArgs -N -e 'SELECT 1' 2>$null | Out-Null
        if (!$LASTEXITCODE) { $connected = $true; break }
    }
    Start-Sleep -Milliseconds 500
}
if (!$connected) { throw "MariaDB did not start. Check $runtime\mariadb-error.log." }

if (!$initialized) {
    $sql = "CREATE DATABASE IF NOT EXISTS smart_school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS 'smart_school'@'127.0.0.1' IDENTIFIED BY '$($keys.database)'; GRANT ALL ON smart_school.* TO 'smart_school'@'127.0.0.1';"
    $sql | & $mysql @mysqlArgs
    if ($LASTEXITCODE) { throw 'Could not initialize the application database user.' }
    $dump = (Join-Path $PSScriptRoot 'database.sql').Replace('\', '/')
    & $mysql @mysqlArgs --abort-source-on-error smart_school -e "source $dump"
    if ($LASTEXITCODE) { throw 'Database restore failed.' }

    $adminPassword = 'School!' + [guid]::NewGuid().ToString('N').Substring(0, 20)
    $env:SMART_SCHOOL_INITIAL_PASSWORD = $adminPassword
    try {
        $hash = & $php -r "echo password_hash(getenv('SMART_SCHOOL_INITIAL_PASSWORD'), PASSWORD_DEFAULT);"
        if ($LASTEXITCODE -or !$hash) { throw 'Could not create the admin password.' }
    } finally {
        Remove-Item Env:SMART_SCHOOL_INITIAL_PASSWORD -ErrorAction SilentlyContinue
    }
    "UPDATE staff SET password='$hash' WHERE id=1;" | & $mysql @mysqlArgs smart_school
    if ($LASTEXITCODE) { throw 'Could not save the admin password.' }
    Set-Content -LiteralPath $readyFile -Value 'Ready'
    Write-Host "Admin email: admin@smartschool.test"
    Write-Host "One-time display of the generated admin password: $adminPassword"
}

if (!(Get-OwnedListener $httpPort $httpPidFile)) {
    $docroot = Join-Path $PSScriptRoot 'app'
    $router = Join-Path $PSScriptRoot 'router.php'
    $web = Start-Process $php -ArgumentList "-d extension=gd -d extension=zip -d mysqli.default_port=$dbPort -S 127.0.0.1:$httpPort -t `"$docroot`" `"$router`"" -WindowStyle Hidden -PassThru -RedirectStandardError (Join-Path $runtime 'php-error.log') -RedirectStandardOutput (Join-Path $runtime 'php.log')
    Set-Content -LiteralPath $httpPidFile -Value $web.Id
}
Write-Host "Open $url/site/login"
