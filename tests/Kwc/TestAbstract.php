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
        Kwf_Component_Data_Root::setShowInvisible(false);
    }

    protected function _init($componentClass)
    {
        Kwf_Component_Data_Root::setComponentClass($componentClass);
        Zend_Session::$_unitTestEnabled = true;
        $this->_root = Kwf_Component_Data_Root::getInstance();
        $this->_root->setFilename('kwf/kwctest/'.$componentClass);
        if (function_exists('apc_clear_cache')) apc_clear_cache('user');
        Kwf_Component_Cache_Memory::getInstance()->_clean();
        Kwf_Cache_Simple::resetZendCache();
        Kwf_Registry::get('config')->debug->componentCache->disable = false;
        Kwf_Config::deleteValueCache('debug.componentCache.disable');
        Kwc_FulltextSearch_MetaModel::setInstance(new Kwf_Model_FnF(array(
            'primaryKey' => 'page_id',
        )));
        Kwf_Assets_Package_Default::clearInstances();
        Kwf_Component_LogDuplicateModel::setInstance(new Kwf_Model_FnF(array(
        )));
        Kwf_Component_Events::clearInstances();
        return $this->_root;
    }

    protected final function _process()
    {
        $filename = $this->_root->filename;

        Kwf_Component_Events::fireEvent(new Kwf_Component_Event_Row_UpdatesFinished());
        Kwf_Component_Data_Root::reset();
        $this->_root = Kwf_Component_Data_Root::getInstance();
        $this->_root->setFilename($filename);
    }
}
