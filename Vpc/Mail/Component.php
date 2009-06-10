<?php
class Vpc_Mail_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['mail'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Paragraphs_Component'
        );
        $ret['modelname'] = 'Vpc_Mail_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['mail'] = $this->getData()->getChildComponent('-mail');
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
     *   <li>%firstname%</li>
     *   <li>%lastname%</li>
     * </ul>
     */
    public function getHtml(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $output = new Vps_Component_Output_Mail();
        $output->setType(Vps_Component_Output_Mail::TYPE_HTML);
        $output->setRecipient($recipient);
        $ret = $output->render($this->getData()->getChildComponent('-mail'));
        if ($recipient) $ret = $this->_replacePlaceholders($ret, $recipient);
        return $ret;
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
        $ret = $output->render($this->getData()->getChildComponent('-mail'));
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
        $pattern = '/\%(.*)\:(.*)\%/U';
        if ($recipient->getMailGender() == Vpc_Mail_Recipient_Interface::MAIL_GENDER_MALE) {
            $text = preg_replace($pattern, '$1', $text);
            $gender = trlVps('Mr.');
        } else {
            $text = preg_replace($pattern, '$2', $text);
            $gender = trlVps('Ms.');
        }
        $text = str_replace('%gender%', $gender, $text);
        $title = $recipient->getMailTitle();
        $search = $title == '' ? '%title% ' : '%title%';
        $text = str_replace($search, $title, $text);
        $text = str_replace('%firstname%', $recipient->getMailFirstname(), $text);
        $text = str_replace('%lastname%', $recipient->getMailLastname(), $text);
        return $text;
    }
}
