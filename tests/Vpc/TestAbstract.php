<?php
abstract class Vpc_TestAbstract extends PHPUnit_Framework_TestCase
{
    protected $_root;

    public function setUp($componentClass)
    {
        Vps_Component_Data_Root::setComponentClass($componentClass);
        $this->_root = Vps_Component_Data_Root::getInstance();
        Vps_Component_Cache::getInstance()->setModel(new Vps_Component_Cache_CacheModel());
        Vps_Component_Cache::getInstance()->setMetaModel(new Vps_Component_Cache_CacheMetaModel());
        Vps_Component_RowObserver::getInstance()->clear();
        Vps_Component_RowObserver::getInstance()->setSkipFnF(false);
    }

    public function tearDown()
    {
        Vps_Component_RowObserver::getInstance()->clear();
        Vps_Component_RowObserver::getInstance()->setSkipFnF(true);
    }

    protected final function _process()
    {
        Vps_Component_RowObserver::getInstance()->process();
        Vps_Component_Data_Root::reset();
        $this->_root = Vps_Component_Data_Root::getInstance();
    }
}