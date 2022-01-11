<?php
class Kwc_Mail_Editable_Trl_PreviewController extends Kwc_Mail_Trl_PreviewController
{
    public function jsonDataAction()
    {
        $mail = $this->_getMailComponent();

        $htmlRenderer = new Kwf_Component_Renderer_Mail();
        $htmlRenderer->setRenderFormat(Kwf_Component_Renderer_Mail::RENDER_HTML);
        $htmlRenderer->setHtmlStyles($mail->getHtmlStyles());

        $textRenderer = new Kwf_Component_Renderer_Mail();
        $textRenderer->setRenderFormat(Kwf_Component_Renderer_Mail::RENDER_TXT);

        $this->view->html = $htmlRenderer->renderComponent($mail->getData());
        $this->view->text = nl2br($textRenderer->renderComponent($mail->getData()));
        $this->view->format = Kwc_Mail_Recipient_Interface::MAIL_FORMAT_HTML;
    }
}
