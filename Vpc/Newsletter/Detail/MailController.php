<?php
class Vpc_Newsletter_Detail_MailController extends Vps_Controller_Action
{
    private function _getQueueRow()
    {
        $model = Vps_Model_Abstract::getInstance('Vpc_Newsletter_QueueModel');
        return $model->getRow($this->_getParam('id'));
    }

    public function jsonDataAction()
    {
        $row = $this->_getQueueRow();
        $recipient = $row->getRecipient();
        $mail = $row->getParentRow('Newsletter')->getMailComponent();

        $this->view->html = $mail->getHtml($recipient);
        $this->view->text = $mail->getText($recipient);
        $this->view->format = $recipient->getMailFormat();
        $this->view->subject = $mail->getSubject($recipient);
    }

    public function jsonSendMailAction()
    {
        $row = $this->_getQueueRow();
        $recipient = $row->getRecipient();
        $mail = $row->getParentRow('Newsletter')->getMailComponent();
        $this->view->message = $mail->send($recipient, null, $this->_getParam('address'), $this->_getParam('format')) ?
            trlVps('E-Mail successfully sent.') :
            trlVps('Error while sending E-Mail.');
    }
}