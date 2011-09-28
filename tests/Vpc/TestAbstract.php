<?php
abstract class Vpc_TestAbstract extends Vps_Test_TestCase
{
    protected $_root;

    public function setUp($componentClass)
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass($componentClass);
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    protected final function _process()
    {
        Vps_Component_ModelObserver::getInstance()->process();
        Vps_Component_Data_Root::reset();
        Vps_Component_Generator_Abstract::clearInstances();
        $this->_root = Vps_Component_Data_Root::getInstance();
    }
}