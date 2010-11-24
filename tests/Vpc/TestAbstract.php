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
        Vps_Component_RowObserver::getInstance()->process();
        Vps_Component_Data_Root::reset();
        $this->_root = Vps_Component_Data_Root::getInstance();
    }
}