<?php
class Kwc_FulltextSearch_MetaModel extends Kwf_Model_Db
{
    protected $_primaryKey = 'page_id';
    protected $_table = 'kwc_fulltext_meta';
    private static $_instance;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (isset(self::$_instance)) {
            return self::$_instance;
        }
        return Kwf_Model_Abstract::getInstance('Kwc_FulltextSearch_MetaModel');
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
