<?php
class Kwc_Mail_Trl_PreviewController extends Kwf_Controller_Action
{
    protected function _getRecipient()
    {
        return null;
    }

    protected function _getMailComponent()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible' => true))
            ->getComponent();
    }

    public function jsonDataAction()
    {
        $recipient = $this->_getRecipient();
        $mail = $this->_getMailComponent();
        $this->view->recipientId = $recipient->id;
        $this->view->html = $mail->getHtml($recipient, false);
        $this->view->text = nl2br($mail->getText($recipient));
        $this->view->format = $recipient ? $recipient->getMailFormat() : Kwc_Mail_Recipient_Interface::MAIL_FORMAT_HTML;
        $this->view->subject = $mail->getSubject($recipient);
    }

    public function jsonSendMailAction()
    {
        $recipient = $this->_getRecipient();
        $mail = $this->_getMailComponent();
        if (!$recipient) throw new Kwf_ClientException(trlKwf('User not found, cannot send testmail.'));
        $this->view->message = $mail->send($recipient, null, $this->_getParam('address'), $this->_getParam('format'), false) ?
            trlKwf('E-Mail successfully sent.') :
            trlKwf('Error while sending E-Mail.');
    }

    public function showHtmlAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8');
        $recipient = $this->_getRecipient();
        $mail = $this->_getMailComponent();
        $this->getResponse()->setBody($mail->getHtml($recipient, false));
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function showTextAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/plain; charset=utf-8');
        $recipient = $this->_getRecipient();
        $mail = $this->_getMailComponent();
        $this->getResponse()->setBody($mail->getText($recipient));
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
