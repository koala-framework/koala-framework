<?php
class Vpc_Mail_Editable_MailsController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $admin = Vpc_Admin::getInstance($this->_getParam('class'));
        $this->view->componentsControllerUrl = $admin->getControllerUrl('Components');
        $this->view->xtype = 'vpc.mail.editable';

        $componentConfigs = array();
        $m = Vps_Model_Abstract::getInstance('Vpc_Mail_Editable_ComponentsModel');
        foreach ($m->getRows() as $row) {
            $cls = $row->content_component_class;
            $admin = Vpc_Admin::getInstance($cls);
            foreach ($admin->getExtConfig() as $k=>$cfg) {
                if (!isset($componentConfigs[$cls.'-'.$k])) {
                    $componentConfigs[$cls.'-'.$k] = $cfg;
                }
            }
        }
        $this->view->componentConfigs = $componentConfigs;
    }
}
