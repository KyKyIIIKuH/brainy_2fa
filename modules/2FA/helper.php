<?php

class TwoFAHelper
{
    /**
     * Module version
     */
    
    const MODULE_VERSION = '1.0';
    
    /**
     * Module parametres file path
     * @var string
     */
    private static $SecretFile = '/usr/local/brainycp/data/2FA/users.dat';
        
    /**
     * Module parametres
     * @var boolean|array
     */
    private static $config = false;
    
    public static function getModuleVersion()
    {
        return self::MODULE_VERSION;
    }
    
    public static function getConfig($param = false)
    {
        $data = @json_decode(file_get_contents(self::$SecretFile), true);
        $res = array();
        if($data[$_SESSION["user"]] == NULL) {
            $data[$_SESSION["user"]]["secret"] = "<span style='color:red;'><b>Ключ не создан</b></span>";
        }
        $res["response"] = ( $data[$_SESSION["user"]] );
        return $res;
    }
    
    public static function getQRCode()
    {
        $data = @json_decode(file_get_contents(self::$SecretFile), true);
        $output = shell_exec("/usr/bin/php72/bin/php -q /usr/local/brainycp/2FA/getqrcode.php username=".escapeshellarg($_SESSION["user"])." secret=".escapeshellarg($data[$_SESSION["user"]]["secret"]));
        return $output;
    }
    
    public static function setQRCode()
    {
        $output = shell_exec("/usr/bin/php72/bin/php -q /usr/local/brainycp/2FA/createqr.php username=".escapeshellarg($_SESSION["user"]));
        return $output;
    }
    
    public static function status2FA()
    {
        $data = @json_decode(file_get_contents(self::$SecretFile), true);
        $messages = "Отключить 2FA";
        if($data[$_SESSION["user"]] == NULL) {
            $messages = "Включить 2FA";
        }
        return $messages;
    }
    
}

?>