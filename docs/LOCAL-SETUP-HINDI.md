# Local system par project kaise chalayein

Is repository mein application source aur fictional demo database dono hain. VS Code source dekhne/edit karne ke liye hai; project ko chalane ke liye **PHP aur MariaDB/MySQL** runtime bhi chahiye.

## Tarika 1: Windows + XAMPP

1. PHP 8.2 aur MariaDB wala compatible XAMPP install karein. Default location `C:\xampp` rakhein.
2. GitHub par **Code → Download ZIP** karein, ya Git se clone karein:

   ```powershell
   git -c core.longpaths=true clone https://github.com/dhanishmaurya0001-gif/SmartSchool-Management-System.git C:\SchoolERP
   ```

3. Complete folder ko chhote writable path par rakhein, jaise `C:\SchoolERP`. Sirf `app/` alag copy na karein; root launcher aur `database.sql` bhi chahiye.
4. `Start-SmartSchool.cmd` par double-click karein.
5. Pehli baar private local database initialize hoga aur demo SQL import hogi. Process complete hone dein.
6. Console mein jo generated initial admin password aaye, use apne paas note kar sakte hain. Yeh aapki local installation ka owner password hai.
7. Browser mein kholen: **http://127.0.0.1:18097/site/login**.
8. Interview demo ke liye login karein:

   - ID: `demo.superadmin@pragati.example`
   - Password: `Demo@School2026!`

Student/Parent login: **http://127.0.0.1:18097/site/userlogin**. Baaki roles ke liye [demo login guide](DEMO-LOGINS.md) dekhein.

Launcher apni loopback-only PHP aur MariaDB instances start karta hai. XAMPP Control Panel ka Apache/MySQL alag se start karna is method ke liye zaroori nahi hai. Web port **18097**, database port **33318** hai. `runtime/` folder mein database aur local keys save hoti hain; is folder ko GitHub par commit na karein.

XAMPP kisi aur drive par hai to project folder mein PowerShell kholkar:

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\Start-SmartSchool.ps1 -XamppPath "D:\xampp"
```

PC restart ke baad wahi launcher dobara run karein. Existing database reuse hogi. Runtime folder ya database files delete karne se aapke demo changes kho sakte hain.

## VS Code mein kya karein

1. **File → Open Folder** se complete repository folder kholen.
2. **Terminal → New Terminal** kholen.
3. Windows launcher chalayein, ya neeche Docker commands use karein.
4. Source PHP files `app/application/` mein milengi. Existing framework/vendor files badalne se pehle test copy/backup rakhein.

**Live Server extension se PHP project nahi chalega.** Browser mein PHP source text ya blank page aaye to runtime URL use karein, file ko seedha open na karein.

## Tarika 2: Docker — Windows, macOS ya Linux

Docker Desktop ya Docker Engine + Compose install aur start karein. Windows par Docker Desktop ki supported Linux-container configuration chahiye.

Repository root terminal mein:

```bash
python tools/create-env.py
docker compose up --build -d
docker compose ps
```

Jahan `python3` command hoti hai, pehli command mein `python3` use karein. Generator local `.env` banata hai; application key aur database passwords public repository mein save nahi karne hain.

Python nahi hai to `.env.example` ki copy `.env` banayein aur `DB_PASSWORD`, `DB_ROOT_PASSWORD`, `SMART_SCHOOL_APP_KEY` mein teen alag strong random values bharein. `APP_URL` local demo ke liye `http://127.0.0.1:8080/` rakhein.

Services healthy hone par:

- Staff: **http://127.0.0.1:8080/site/login**
- Student/Parent: **http://127.0.0.1:8080/site/userlogin**

Status aur errors ke liye:

```bash
docker compose ps
docker compose logs --tail=100 app db
```

Compose configuration validate hui hai; successful image build/container operation ka alag verification chahiye. Native Windows checks ko Docker acceptance test na samjhein.

## Stop, restart aur saved data

```bash
docker compose stop
docker compose start
```

`docker compose down` containers hata sakta hai, lekin named volumes rakhta hai. **Normal stop/reset ke liye `docker compose down -v` use na karein**—`-v` database aur upload volumes delete karta hai.

`database.sql` sirf empty database volume ke first startup par import hoti hai. SQL file edit/rebuild karne se existing records automatically reset nahi hote. Database aur upload volume ke backups saath rakhein.

## Manual PHP + MariaDB setup

Experienced users apna Apache/PHP/MySQL stack use kar sakte hain:

1. Ek naya testing database aur us database tak limited application user banayein.
2. Root `database.sql` import karein. Existing real school database ke upar import na karein.
3. PHP process ke environment mein ye variables configure karein:

   ```text
   DB_HOST
   DB_PORT
   DB_NAME
   DB_USER
   DB_PASSWORD
   APP_URL
   SMART_SCHOOL_APP_KEY
   ```

4. Apache document root `app/` ho, rewrite support enabled ho, aur private source directories browser se accessible na hon.
5. PHP mysqli, GD, mbstring, XML/DOM, curl, fileinfo, OpenSSL aur ZIP support available ho. PDF temporary directory aur required upload directories writable hon.
6. Staff ya Student/Parent login URL se demo account use karein.

Bundled vendor libraries included hain; normal setup mein framework/dependencies ko arbitrary newer versions se replace na karein.

## Common problems

| Problem | Kya check karein |
|---|---|
| Link nahi khul raha | Launcher/containers running hain? Sahi method ka port use kiya hai? |
| Port busy | Error mein diya port kisi aur service ne use kiya hai; conflicting service identify karein. |
| Database connection fail | DB service ready hai, database imported hai, aur environment values match hain? |
| Admission/QR/barcode error | PHP GD enabled hai? |
| PDF write error | Application PDF temporary directory writable hai? |
| Old data dikh raha | Logout/login karein; correct session `2026–27` aur correct local installation select karein. |
| Docker command fails | Docker Engine running hai? Compose configuration aur service logs dekhein. |

Demo logins sirf fictional local testing ke liye hain. Real school deployment mein public demo passwords, empty provider configuration ya demo database use na karein.
