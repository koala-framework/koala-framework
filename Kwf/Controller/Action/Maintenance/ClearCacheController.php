<?php
class Kwf_Controller_Action_Maintenance_ClearCacheController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->typeNames = Kwf_Util_ClearCache::getInstance()->getTypeNames();
        $this->view->assetsType = 'Kwf_Controller_Action_Maintenance:ClearCache';
        $this->view->xtype = 'kwf.maintenance.clearCache';
    }

    public function jsonClearCacheAction()
    {
        $c = new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum'));
        $options = array(
            'progressAdapter' => $c
        );
        Kwf_Util_ClearCache::getInstance()->clearCache(
            $this->_getParam('types'),
            false, //output
            true,  //refresh
            $options
        );
    }
}