<?php
class Vpc_Mail_Component extends Vpc_Abstract
{
    private $_mailData;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Paragraphs_Component'
        );
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
     * Gibt den personalisierten HTML-Quelltext der Mail zurück
     *
     * Ersetzt folgende Platzhalter:
     * <ul>
     *   <li>%Text bei Mann:Text bei Frau%</li>
     *   <li>%gender%</li>Durch "Mr." oder "Ms."
     *   <li>%title%</li>Falls leer, wird nachfolgendes Leerzeichen gelöscht
     *   <li>%firstname%</li>Falls leer, wird nachfolgendes Leerzeichen gelöscht
     *   <li>%lastname%</li>
     * </ul>
     */
    public function getHtml(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $output = new Vps_Component_Output_Mail();
        $output->setType(Vps_Component_Output_Mail::TYPE_HTML);
        $output->setRecipient($recipient);
        $ret = $output->render($this->getData());
        if ($recipient) $ret = $this->_replacePlaceholders($ret, $recipient);
        return $ret;
    }

    /**
     * Verschickt ein mail an @param $recipient.
     * @param $data Optionale Daten die benötigt werden, kann von den
     *        Komponenten per $this->getData()->getParentByClass('Vpc_Mail_Component')->getComponent()->getMailData();
     *        ausgelesen werden
     */
    public function send(Vpc_Mail_Recipient_Interface $recipient, $data = null, $toAddress = null)
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
        if ($recipient->getMailFormat() == Vpc_Mail_Recipient_Interface::MAIL_FORMAT_HTML) {
            $mail->setBodyHtml($this->getHtml($recipient));
        }
        $mail->setBodyText($this->getText($recipient));
        $mail->setSubject($this->getSubject($recipient));
        if ($this->getRow()->from_email) {
            $mail->setFrom($this->getRow()->from_email, $this->getRow()->from_name);
        }
        if ($this->getRow()->reply_email) {
            $mail->addHeader('Reply-To', $this->getRow()->reply_email);
        }
        //TODO: attachments, inline images
        return $mail->send();
    }

    //kann von einer mail-content komponente aufgerufen werden
    //hier können mail spezifische daten drinstehen
    public function getMailData()
    {
        return $this->_mailData;
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
        if ($recipient) $ret = $this->_replacePlaceholders($ret, $recipient);
        return $ret;
    }

    public function getSubject(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $ret = $this->getRow()->subject;
        if ($recipient) $ret = $this->_replacePlaceholders($ret, $recipient);
        return $ret;
    }

    public function getLinks()
    {
        throw new Vps_Exception('Not implemented yet.');
    }

    protected function _replacePlaceholders($text, Vpc_Mail_Recipient_Interface $recipient)
    {
        // gender
        $pattern = '/\%(.*)\:(.*)\%/U';
        if ($recipient->getMailGender() == Vpc_Mail_Recipient_Interface::MAIL_GENDER_MALE) {
            $text = preg_replace($pattern, '$1', $text);
            $gender = trlVps('Mr.');
        } else {
            $text = preg_replace($pattern, '$2', $text);
            $gender = trlVps('Ms.');
        }
        $text = str_replace('%gender%', $gender, $text);
        // title
        $title = $recipient->getMailTitle();
        $search = $title == '' ? '%title% ' : '%title%';
        $text = str_replace($search, $title, $text);
        // firstname
        $firstname = $recipient->getMailFirstname();
        $search = $firstname == '' ? '%firstname% ' : '%firstname%';
        $text = str_replace($search, $firstname, $text);
        // lastname
        $text = str_replace('%lastname%', $recipient->getMailLastname(), $text);
        return $text;
    }
}
