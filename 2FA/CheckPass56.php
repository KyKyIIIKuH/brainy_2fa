<?
$data = array();
foreach ($argv as &$value) {
    $array_params = explode("=", $value);
    if(isset($array_params[1]) && empty($array_params[1]) || !isset($array_params[1]) && empty($array_params[1])) continue;
    $data[$array_params[0]] = (($array_params[1]));
}

require_once "/usr/local/brainycp/classes/server.php";
require_once "/etc/brainy/globals.php";

/*
Функция ниже принадлежит https://brainycp.com/
*/
function CheckPass($login, $pass)
{
    $server = new server();
    $comm1 = "sudo cat /etc/shadow | grep ^";
    $comm1 .= $login . ":";
    ob_start();
    $res1 = system($comm1, $retval);
    ob_clean();
    $shadow = explode("\$", $res1);
    $encrypter = $shadow[1];
    $check_round = 0;
    if (preg_match("/rounds=[0-9]+/i", $res1)) {
        $salt = $shadow[2] . "\$" . $shadow[3];
        $passhash = explode(":", $shadow[4]);
        $check_round = 1;
    } else {
        $salt = $shadow[2];
        $passhash = explode(":", $shadow[3]);
    }
    $passhash = $passhash[0];
    if (!is_file("/usr/bin/python")) {
        $comm2 = "python2 -c 'import crypt; print crypt.crypt(";
    } else {
        $comm2 = "python -c 'import crypt; print crypt.crypt(";
    }
    $comm2 .= "\"";
    $comm2 .= $server->strip_doublequotes_cmd($server->escape_slash_echoe($server->strip_quotes_cmd_auth($pass)));
    $comm2 .= "\", \"\$";
    $comm2 .= $encrypter;
    $comm2 .= "\$";
    $comm2 .= $salt;
    $comm2 .= "\")";
    $comm2 .= "'";
    ob_start();
    $res2 = system($comm2, $retval);
    ob_clean();
    $res2 = explode("\$", $res2);
    $res2 = $check_round ? $res2[4] : $res2[3];
    if ($passhash == $res2) {
        return true;
    }
    return false;
}

echo CheckPass($data["username"], $data["password"]);
