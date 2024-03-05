<?php
abstract class Kwf_Test_Kwc_TestCase extends Kwf_Test_TestCase
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
        Kwf_Component_PagesMetaModel::setInstance(new Kwf_Component_PagesMetaModel(array(
            'proxyModel' => new Kwf_Model_FnF(array(
                'primaryKey' => 'page_id',
            )))
        ));
        Kwf_Component_LogDuplicateModel::setInstance(new Kwf_Model_FnF(array(
        )));
        Kwf_Media_OutputCache::getInstance()->clean();
        Kwf_Events_Subscriber::clearInstances();
        return $this->_root;
    }

    protected final function _process()
    {
        $filename = $this->_root->filename;

        Kwf_Events_ModelObserver::getInstance()->process();
        Kwf_Component_Data_Root::reset();
        $this->_root = Kwf_Component_Data_Root::getInstance();
        $this->_root->setFilename($filename);
    }
}
