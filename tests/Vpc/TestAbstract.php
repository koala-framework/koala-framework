<?php
abstract class Vpc_TestAbstract extends Vps_Test_TestCase
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
        Vps_Component_Cache::saveStaticMeta();
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