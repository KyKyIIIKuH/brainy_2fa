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
include_once __DIR__.'/Users.php';

if($data["username"] == NULL) {
    echo NULL;
    die();
}

$users = new Users();
$user = $users->loadUser($data['username']);

if ($user) {
    $g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
    $secret = $g->generateSecret();
    
    $qrcode = @json_decode(file_get_contents( __DIR__.'/../data/2FA/users.dat' ), true);
    $qrcode[$data['username']] = array();
    $qrcode[$data['username']]["secret"] = $secret;
    file_put_contents( __DIR__.'/../data/2FA/users.dat', json_encode($qrcode));
    echo $secret;
    die();
}

?>