<?php
class Kwf_Controller_Action_Ext4_Ext4Controller extends Zend_Controller_Action
{
    public function indexAction()
    {
        $view = new Kwf_View_Ext4();
        $this->getHelper('viewRenderer')->setView($view);
        $resource = $this->_getParam('resource');
        $resource = Kwf_Acl::getInstance()->get($resource);
        if (!$resource) throw new Kwf_Exception_NotFound();
        if (!$resource instanceof Kwf_Acl_Resource_MenuExt4) throw new Kwf_Exception_NotFound();
        $view->extController = $resource->getExtController();
    }
}
