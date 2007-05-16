<?php
class Vps_Controller_Action_Admin extends Vps_Controller_Action_Login_Abstract
{
    // Ajax
    public function sessionAction()
    {
        $param = $this->getRequest()->getParam("param");
        $value = $this->getRequest()->getParam("value");

        $adminSession = new Zend_Session_Namespace('admin');
        $adminSession->$param = $value;
    }
}
