<?php
// Render terminates TLS in front of Apache. Trust the configured canonical URL,
// not arbitrary client-supplied forwarding headers. This file is deployment-only.
$canonical = getenv('APP_URL') ?: getenv('RENDER_EXTERNAL_URL');
if ($canonical && parse_url($canonical, PHP_URL_SCHEME) === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = '443';
    $_SERVER['REQUEST_SCHEME'] = 'https';
}
