<?php
class Kwf_Component_PagesMetaModel extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwf_pages_meta';
    protected $_primaryKey = 'page_id';
    protected $_rowClass = 'Kwf_Component_PagesMetaRow';
    private static $_instance;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (isset(self::$_instance)) {
            return self::$_instance;
        }
        return Kwf_Model_Abstract::getInstance('Kwf_Component_PagesMetaModel');
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
