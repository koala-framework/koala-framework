<?php
class Kwf_Component_LogDuplicateModel extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_log_duplicate';
    private static $_instance;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (isset(self::$_instance)) {
            return self::$_instance;
        }
        return Kwf_Model_Abstract::getInstance('Kwf_Component_LogDuplicateModel');
    }

    public static function clearInstance()
    {
        self::$_instance = null;
    }

    public static function setInstance($instance)
    {
        self::$_instance = $instance;
    }
}
