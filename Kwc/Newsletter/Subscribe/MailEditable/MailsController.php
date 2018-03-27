<?php
class Kwc_Newsletter_Subscribe_MailEditable_MailsController extends Kwc_Mail_Editable_MailsController
{
    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'kwc.newsletter.subscribe.mailEditable';

        $admin = Kwc_Admin::getInstance($this->_getParam('class'));
        $this->view->componentsControllerUrl = $admin->getControllerUrl('Components');
    }
}
