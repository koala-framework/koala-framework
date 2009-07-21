<?php
class Vpc_Mail_PreviewController extends Vps_Controller_Action
{
    protected function _getRecipient()
    {
        return null;
    }

    protected function _getMailComponent()
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'))
            ->getComponent();
    }

    public function jsonDataAction()
    {
        $recipient = $this->_getRecipient();
        $mail = $this->_getMailComponent();
        $this->view->html = $mail->getHtml($recipient);
        $this->view->text = $mail->getText($recipient);
        $this->view->format = $recipient ? $recipient->getMailFormat() : Vpc_Mail_Recipient_Interface::MAIL_FORMAT_HTML;
        $this->view->subject = $mail->getSubject($recipient);
    }

    public function jsonSendMailAction()
    {
        $recipient = $this->_getRecipient();
        $mail = $this->_getMailComponent();
        if (!$recipient) throw new Vps_ClientException(trlVps('User not found, cannot send testmail.'));
        $this->view->message = $mail->send($recipient, null, $this->_getParam('address'), $this->_getParam('format')) ?
            trlVps('E-Mail successfully sent.') :
            trlVps('Error while sending E-Mail.');
    }

    public function showHtmlAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8');
        $recipient = $this->_getRecipient();
        $mail = $this->_getMailComponent();
        echo $mail->getHtml($recipient);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function showTextAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/plain; charset=utf-8');
        $recipient = $this->_getRecipient();
        $mail = $this->_getMailComponent();
        echo $mail->getText($recipient);
        $this->_helper->viewRenderer->setNoRender(true);
    }
}