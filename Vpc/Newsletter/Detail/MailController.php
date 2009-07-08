<?php
class Vpc_Newsletter_Detail_MailController extends Vps_Controller_Action
{
    private function _getNewsletterRow()
    {
        $model = Vps_Model_Abstract::getInstance('Vpc_Newsletter_QueueModel');
        return $model->getRow($this->_getParam('id'));
    }

    public function jsonDataAction()
    {
        $row = $this->_getNewsletterRow();
        $recipient = Vpc_Newsletter_Queue::getRecipient($row);
        $mail = Vpc_Newsletter_Queue::getMailComponent($row);

        $this->view->html = $mail->getHtml($recipient);
        $this->view->subject = $mail->getSubject($recipient);
    }

    public function jsonSendMailAction()
    {
        $row = $this->_getNewsletterRow();
        $recipient = Vpc_Newsletter_Queue::getRecipient($row);
        $mail = Vpc_Newsletter_Queue::getMailComponent($row);
        $this->view->message = $mail->send($recipient, null) ? // TODO: richtige Mail
            trlVps('E-Mail successfully sent.') :
            trlVps('Error while sending E-Mail.');
    }
}