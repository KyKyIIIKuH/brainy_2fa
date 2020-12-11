<?

class TwoFAController
{
    /**
     * Now action
     * @var string
     */
    private $action;
    
    /**
     * Smarty
     * @var object
     */
    public $smarty;
    
    /**
     * Smarty template
     * @var object
     */
    
    public $tpl;
    
    /**
     * @var 2FAController
     */
    
    private static $instance;
        
    /**
     * Instantiate and return a factory.
     * @return 2FAController
     */
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Start init page
     * @param object $smarty
     * @param object $tpl
     */
     
    public function init($smarty, $tpl)
    {
        $this->smarty = $smarty;
        $this->tpl = $tpl;
        
        $this->setAction();
        
        if (!method_exists($this, $this->action . 'Action')) {
            $this->action = 'default';
        }
        
        $methodName = $this->action . 'Action';
        $this->$methodName();
    }

    /**
     * Default index page
     */
    
    private function defaultAction()
    {
        $this->smarty->assign('twofaconf', TwoFAHelper::getConfig());
        $this->smarty->assign('twofaqrcode', TwoFAHelper::getQRCode());
        $this->smarty->assign('status_2FA', TwoFAHelper::status2FA());
        $this->tpl->out = $this->smarty->fetch('2FA/index.tpl');
    }
    
    private function createqrAction()
    {
        $data = @json_decode(file_get_contents( __DIR__.'/../../data/2FA/users.dat' ), true);
        if($data[$_SESSION["user"]] == NULL) {
            $data[$_SESSION["user"]] = array();
            $data[$_SESSION["user"]]["secret"] = "";
            file_put_contents( __DIR__.'/../../data/2FA/users.dat', json_encode($data));
        }
        
        $output = shell_exec("/usr/bin/php72/bin/php -q /etc/brainy/2FA/createqr.php username=".escapeshellarg($_SESSION["user"]));
        header("Location: ?do=2FA");
    }
    
    private function enable2faAction() {
        header("Location: ?do=2FA&subdo=createqr");
    }
    
    private function dissable2faAction() {
        $data = @json_decode(file_get_contents( __DIR__.'/../../data/2FA/users.dat' ), true);
        if($data[$_SESSION["user"]] != NULL) {
            unset($data[$_SESSION["user"]]) ;
            file_put_contents( __DIR__.'/../../data/2FA/users.dat', json_encode($data));
        }
        header("Location: ?do=2FA");
    }
    
    /**
     * Auto set action from REQUEST
     */
    
    public function setAction()
    {
        if (isset($_REQUEST['subdo'])) {
            $this->action = $_REQUEST['subdo'];
        }
    }
}