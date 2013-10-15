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
        apc_clear_cache('user');
        Kwf_Cache::factory('Core', 'Memcached', array(
            'lifetime'=>null,
            'automatic_cleaning_factor' => false,
            'automatic_serialization'=>true))->clean();
        if (Kwf_Cache_Simple::getBackend() != 'memcache') {
            Kwf_Component_Cache_Memory::getZendCache()->clean();
        }
        Kwf_Cache_Simple::resetZendCache();
        Kwf_Registry::get('config')->debug->componentCache->disable = false;
        Kwf_Config::deleteValueCache('debug.componentCache.disable');
        Kwc_FulltextSearch_MetaModel::setInstance(new Kwf_Model_FnF(array(
            'primaryKey' => 'page_id',
        )));
        return $this->_root;
    }

    protected final function _process()
    {
        $filename = $this->_root->filename;

        Kwf_Component_ModelObserver::getInstance()->process();
        Kwf_Component_Data_Root::reset();
        $this->_root = Kwf_Component_Data_Root::getInstance();
        $this->_root->setFilename($filename);
    }
}
