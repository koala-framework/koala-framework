<?php
abstract class Vpc_TestAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * @var Vps_Component_Data_Root
     */
    protected $_root;

    public function setUp($componentClass)
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass($componentClass);
        $this->_root = Vps_Component_Data_Root::getInstance();
        $this->_root->setFilename('vps/vpctest/'.$componentClass);
        Vps_Component_Cache::getInstance()->setModel(new Vps_Component_Cache_CacheModel());
        Vps_Component_Cache::getInstance()->setMetaModel(new Vps_Component_Cache_CacheMetaModel());
        Vps_Component_Cache::getInstance()->setFieldsModel(new Vps_Component_Cache_CacheFieldsModel());
        Vps_Component_ModelObserver::getInstance()->clear();
        Vps_Component_ModelObserver::getInstance()->setSkipFnF(false);
        Vps_Component_ModelObserver::getInstance()->setDisableCache(false);
        Vps_Media::getOutputCache()->clean();
        Vps_Component_Cache::refreshStaticCache();
    }

    public function tearDown()
    {
        Vps_Component_ModelObserver::getInstance()->clear();
        Vps_Component_ModelObserver::getInstance()->setSkipFnF(true);
        Vps_Component_Data_Root::reset();
        Vps_Component_Cache::clearInstance();
        Vps_Component_Data_Root::reset();
        Vps_Component_Generator_Abstract::clearInstances();
        parent::tearDown();
    }

    protected final function _process()
    {
        Vps_Component_ModelObserver::getInstance()->process();
        Vps_Component_Data_Root::reset();
        Vps_Component_Generator_Abstract::clearInstances();
        $this->_root = Vps_Component_Data_Root::getInstance();
        $this->_root->setFilename('vps/vpctest/'.Vps_Component_Data_Root::getComponentClass());
    }
}