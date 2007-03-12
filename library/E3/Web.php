<?php
class E3_Web {
    
    protected static $_instance = null;
    
    private function __construct(){}
    
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    public function getPageByPath($path)
    {
        $pagenotfound = false;
        if ($pagenotfound) {
            return null;
        } else {
            require_once 'E3/Component/Textbox.php';
            return new E3_Component_Textbox();
        }
    }
    
}

?>
