<?php
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if (preg_match('~^/(?:application|system|temp|addons|backup)(?:/|$)|(?:^|/)\.|\\\\~i', $path)) {
    http_response_code(404);
    return;
}
$file = realpath(__DIR__ . '/app' . $path);
$root = realpath(__DIR__ . '/app');
if ($file && str_starts_with($file, $root . DIRECTORY_SEPARATOR) && is_file($file) && !preg_match('~\.(?:php\d*|phtml|phar|sql|ini|log)$~i', $file)) {
    return false;
}
chdir($root);
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';
require $root . '/index.php';
