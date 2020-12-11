<?php declare(strict_types=1);
header('Content-Type: application/json');

$data = array();
foreach ($argv as &$value) {
    $array_params = explode("=", $value);
    if(isset($array_params[1]) && empty($array_params[1]) || !isset($array_params[1]) && empty($array_params[1])) continue;
    $data[$array_params[0]] = (($array_params[1]));
}

ob_start(); //i'm too lazy to check when is sent what ;)
//set session cookie to be read only via http and not by JavaScript
ini_set('session.cookie_httponly', '1');

// Подключаем composer
require_once __DIR__.'/composer/vendor/autoload.php';

if($data["secret"] == NULL) {
    echo NULL;
    die();
}

echo \Sonata\GoogleAuthenticator\GoogleQrUrl::generate($data["username"], $data["secret"], '2FA BrainyCP');

?>