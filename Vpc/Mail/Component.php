<?php
class Vpc_Mail_Component extends Vpc_Abstract
{
    private $_mailData;
    protected $_images = array();

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Paragraphs_Component'
        );
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Mail/PreviewWindow.js';
        $ret['plugins']['placeholders'] = 'Vpc_Mail_PlaceholdersPlugin';
        $ret['modelname'] = 'Vpc_Mail_Model';
        $ret['componentName'] = 'Mail';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->getData()->getChildComponent('-content');
        return $ret;
    }

    /**
     * Verschickt ein mail an @param $recipient.
     * @param $data Optionale Daten die benötigt werden, kann von den
     *        Komponenten per $this->getData()->getParentByClass('Vpc_Mail_Component')->getComponent()->getMailData();
     *        ausgelesen werden
     */
    public function send(Vpc_Mail_Recipient_Interface $recipient, $data = null, $toAddress = null, $format = null)
    {
        $this->_mailData = $data;

        $mail = new Vps_Mail();
        $name = $recipient->getMailFirstname() . ' ' . $recipient->getMailLastname();
        if (!$name == ' ') $name = null;
        if ($toAddress) {
            $mail->addTo($toAddress, $name);
        } else {
            $mail->addTo($recipient->getMailEmail(), $name);
        }

        if ((!$format && $recipient->getMailFormat() == Vpc_Mail_Recipient_Interface::MAIL_FORMAT_HTML) ||
            $format == Vpc_Mail_Recipient_Interface::MAIL_FORMAT_HTML)
        {
            $mail->setBodyHtml($this->getHtml($recipient, true));
        }
        $mail->setBodyText($this->getText($recipient));
        $mail->setSubject($this->getSubject($recipient));
        if ($this->getRow()->from_email) {
            $mail->setFrom($this->getRow()->from_email, $this->getRow()->from_name);
        }
        if ($this->getRow()->reply_email) {
            $mail->addHeader('Reply-To', $this->getRow()->reply_email);
        }

        if ($this->_images){
            $mail->setType(Zend_Mime::MULTIPART_RELATED);
            foreach ($this->_images as $image) {
                $mail->addAttachment($image);
            }
        }

        //TODO: attachments
        return $mail->send();
    }

    //kann von einer mail-content komponente aufgerufen werden
    //hier können mail spezifische daten drinstehen
    public function getMailData()
    {
        return $this->_mailData;
    }

    /**
     * Gibt den personalisierten HTML-Quelltext der Mail zurück
     *
     * @param bool forMail: ob images als attachment angehängt werden sollen oder nicht
     */
    public function getHtml(Vpc_Mail_Recipient_Interface $recipient = null, $forMail = false)
    {
        $output = new Vps_Component_Output_Mail();
        $output->setType(Vps_Component_Output_Mail::TYPE_HTML);
        $output->setRecipient($recipient);
        $output->setViewClass($forMail ? 'Vps_View_ComponentMail' : 'Vps_View_Component');
        $ret = $output->render($this->getData());
        return $this->processPlaceholder($ret, $recipient);
    }

    /**
     * Gibt den personalisierten Quelltext der Mail zurück
     *
     * @see getHtml Für Ersetzungen siehe
     */
    public function getText(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $output = new Vps_Component_Output_Mail();
        $output->setType(Vps_Component_Output_Mail::TYPE_TXT);
        $output->setRecipient($recipient);
        $ret = $output->render($this->getData());
        $ret = str_replace('&nbsp;', ' ', $ret);
        return $this->processPlaceholder($ret, $recipient);
    }

    public function getSubject(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $ret = $this->getRow()->subject;
        return $this->processPlaceholder($ret, $recipient);
    }

    protected function processPlaceholder($ret, Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $plugins = $this->_getSetting('plugins');
        $p = $plugins['placeholders'];
        $p = new $p($this->getData()->componentId);
        return $p->processMailOutput($ret, $recipient);
    }

    public function getPlaceholders(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $ret = array();
        if ($recipient) {
            $ret['firstname'] = $recipient->getMailFirstname();
            $ret['lastname'] = $recipient->getMailLastname();
        }
        return $ret;
    }

    public function getLinks()
    {
        throw new Vps_Exception('Not implemented yet.');
    }

    public function addImage(Zend_Mime_Part $image)
    {
        $this->_images[] = $image;
    }
}
