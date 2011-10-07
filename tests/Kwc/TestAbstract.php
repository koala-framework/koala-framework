<?php
abstract class Kwc_TestAbstract extends Kwf_Test_TestCase
{
    /**
     * @var Kwf_Component_Data_Root
     */
    protected $_root;

    public function setUp($componentClass = null)
    {
        parent::setUp();
        if ($componentClass) $this->_init($componentClass);
    }

    protected function _init($componentClass)
    {
        Kwf_Component_Data_Root::setComponentClass($componentClass);
        $this->_root = Kwf_Component_Data_Root::getInstance();
        $this->_root->setFilename('kwf/kwctest/'.$componentClass);
        apc_clear_cache('user');
        Kwf_Registry::get('config')->debug->componentCache->disable = false;
        Kwf_Config::deleteValueCache('debug.componentCache.disable');
        return $this->_root;
    }

    protected final function _process()
    {
        Kwf_Component_ModelObserver::getInstance()->process();
        Kwf_Component_Data_Root::reset();
        Kwf_Component_Generator_Abstract::clearInstances();
        $this->_root = Kwf_Component_Data_Root::getInstance();
        $this->_root->setFilename('kwf/kwctest/'.Kwf_Component_Data_Root::getComponentClass());
        apc_clear_cache('user');
    }
}