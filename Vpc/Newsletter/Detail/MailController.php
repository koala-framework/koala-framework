<?php
class Vpc_Newsletter_Detail_MailController extends Vps_Controller_Action
{
    private function _getRecipient()
    {
        $model = Vps_Model_Abstract::getInstance('Vpc_Newsletter_QueueModel');
        $row = $model->getRow($this->_getParam('id'));
        $mailModel = Vps_Model_Abstract::getInstance($row->recipient_model);
        return $mailModel->getRow($row->recipient_id);
    }

    private function _getMailComponent()
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible' => true))
            ->getChildComponent('-mail')->getComponent();
    }

    public function jsonDataAction()
    {
        $component = $this->_getMailComponent();
        $recipient = $this->_getRecipient();

        $this->view->html = $component->getHtml($recipient);
        $this->view->subject = $component->getSubject($recipient);
    }

    public function jsonSendMailAction()
    {
        $component = $this->_getMailComponent();
        $recipient = $this->_getRecipient();
        $this->view->message = $component->send($recipient) ?
            trlVps('E-Mail successfully sent.') :
            trlVps('Error while sending E-Mail.');
    }
}