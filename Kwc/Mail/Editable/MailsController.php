<?php
class Kwc_Mail_Editable_MailsController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $admin = Kwc_Admin::getInstance($this->_getParam('class'));
        $this->view->componentsControllerUrl = $admin->getControllerUrl('Components');
        $this->view->xtype = 'kwc.mail.editable';

        $componentConfigs = array();
        $m = Kwf_Model_Abstract::getInstance('Kwc_Mail_Editable_ComponentsModel');
        foreach ($m->getRows() as $row) {
            $cls = $row->content_component_class;
            $admin = Kwc_Admin::getInstance($cls);
            foreach ($admin->getExtConfig() as $k=>$cfg) {
                if (!isset($componentConfigs[$cls.'-'.$k])) {
                    $componentConfigs[$cls.'-'.$k] = $cfg;
                }
            }
        }
        $this->view->componentConfigs = $componentConfigs;
    }
}
