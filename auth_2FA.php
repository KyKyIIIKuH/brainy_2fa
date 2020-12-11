<?php
$login_panel = (@$_POST["login"] ? @$_POST["login"] : @$_POST["login_panel"]);
$password_panel = (@$_POST["password"] ? @$_POST["password"] : @$_POST["password_panel"]);
$lan_panel = @$_POST["lan"];

$host = (stripos(@$_SERVER['HTTPS'],'ON') === 0 ? 'https://' : 'http://')."{$_SERVER["SERVER_ADDR"]}:".$_SERVER["SERVER_PORT"];

if(isset($login_panel) && empty($login_panel) || !isset($login_panel) && empty($login_panel)) {
    header("Location: {$host}");
    die();
}

$file_auth_brainy = "auth_brainy.php";

// BrainyCP
require_once "./conf/globals.php";
require_once "./lib/punycode/idna_convert.php";
require_once $GLOBALS["SERVER_PHP_PATH"];
$server = new Server();
$server->define();
$server->load();
// BrainyCP

// Templates
$smarty = new Smartest();
$tpl = new tpl();

$conf_template = parse_ini_file($GLOBALS["PROPERTIES_CONF"]);
$template_path = "/tpl/default/";
if (isset($conf_template["template"])) {
    $smarty->assign("template_path", $conf_template["template"]);
    $template_path = $conf_template["template"];
} else {
    $smarty->assign("template_path", "/tpl/default/");
}

// Templates

// Проверяем пароль пользователя
function check_auth2FA($username, $password) {
    $output = shell_exec('/usr/bin/php72/bin/php -q /etc/brainy/2FA/api.php action=check_auth username='.escapeshellarg($username).' password='.escapeshellarg($password));
    return json_decode($output);
}

// Проверяем существует ли пользователь в 2FA
function check_user2FA($username, $password) {
    $output = shell_exec('/usr/bin/php72/bin/php -q /etc/brainy/2FA/api.php action=check_user username='.escapeshellarg($username).' password='.escapeshellarg($password));
    return json_decode($output);
}

// Проверяем подключил ли пользователь 2FA
if(check_user2FA($login_panel, $password_panel)->user == false) {
    $login = urlencode($login_panel);
    $password = urlencode($password_panel);
    
    // Проверяем правильность пароля
    $check_auth = check_auth2FA($login_panel, $password_panel)->auth;
    if($check_auth == false && $check_auth != NULL) {
        header("Location: {$host}/index.php?failedauth=1");
        die();
    }
    
    header("Location: {$host}/{$file_auth_brainy}?user={$login}&pass={$password}");
    die();
}

// Отправляем данные для получения данных 2FA
function send2FA($username, $password, $otp) {
    $output = shell_exec("/usr/bin/php72/bin/php -q /etc/brainy/2FA/api.php username=".escapeshellarg($username)." password=".escapeshellarg($password)." otp={$otp}");
    return json_decode($output);
}

if(isset($_POST["login_panel"]) && !empty($_POST["login_panel"]) && isset($_POST["lan_panel"]) && !empty($_POST["lan_panel"]) && isset($_POST["password_panel"]) && !empty($_POST["password_panel"]) && isset($_POST["otp"]) && !empty($_POST["otp"])) {
    // Проверяем правильность пароля
    if(check_auth2FA($login_panel, $password_panel)->auth == false) {
        header("Location: {$host}/index.php?failedauth=1");
        die();
    }
    
    $response = send2FA($_POST["login_panel"], $_POST["password_panel"], $_POST["otp"]);
    
    if($response->error == false && $response->messages == "login ok") {
        $login = urlencode($_POST["login_panel"]);
        $password = urlencode($_POST["password_panel"]);
        header("Location: {$host}/{$file_auth_brainy}?user={$login}&pass={$password}");
        exit();
    }
    
    if($response->error == true) {
        header("Location: {$host}/index.php?failedauth=1");
    }
    exit();
}

$smarty->assign("login_panel", $login_panel);
$smarty->assign("password_panel", $password_panel);
$smarty->assign("lan_panel", $lan_panel);

$tpl->out = $tpl->load_tpl("auth_2FA.tpl");
echo $tpl->out;

?>