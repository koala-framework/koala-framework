<?php
abstract class Vpc_TestAbstract extends Vps_Test_TestCase
{
    /**
     * @var Vps_Component_Data_Root
     */
    protected $_root;

    public function setUp($componentClass = null)
    {
        parent::setUp();
        if ($componentClass) $this->_init($componentClass);
    }

    protected function _init($componentClass)
    {
        Vps_Component_Data_Root::setComponentClass($componentClass);
        $this->_root = Vps_Component_Data_Root::getInstance();
        $this->_root->setFilename('vps/vpctest/'.$componentClass);
        apc_clear_cache('user');
        return $this->_root;
    }

    protected final function _process()
    {
        Vps_Component_ModelObserver::getInstance()->process();
        Vps_Component_Data_Root::reset();
        Vps_Component_Generator_Abstract::clearInstances();
        $this->_root = Vps_Component_Data_Root::getInstance();
        $this->_root->setFilename('vps/vpctest/'.Vps_Component_Data_Root::getComponentClass());
        apc_clear_cache('user');
    }
}