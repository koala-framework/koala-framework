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
        $ret['generators']['redirect'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Mail_Redirect_Component',
            'name' => 'r'
        );

        $ret['default'] = array(
            'from_email' => 'el@vivid-planet.com', //TODO: dieser standardwert macht selten sinn
            'from_name' => 'Erich Lechenauer',
        );

        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Mail/PreviewWindow.js';
        $ret['plugins']['placeholders'] = 'Vpc_Mail_PlaceholdersPlugin';
        $ret['modelname'] = 'Vpc_Mail_Model';
        $ret['componentName'] = 'Mail';

        // set shorter source keys for recipient models
        // key = sourceShortcut, value = modelClass
        // e.g. array('user' => 'Vps_User_Model')
        $ret['recipientSources'] = array();

        $ret['mailHtmlStyles'] = array();
        $ret['bcc'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $c = $this->getData()->getChildComponent('-content');
        if ($c) {
            $ret['content'] = $c;
        }
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

        if ($this->_getSetting('bcc')) {
            $mail->addBcc($this->_getSetting('bcc'));
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
        $ret = $this->_processPlaceholder($ret, $recipient);
        $ret = $this->getData()->getChildComponent('_redirect')->getComponent()->replaceLinks($ret, $recipient);
        if ($this->_getSetting('mailHtmlStyles')) {
            $p = new Vpc_Mail_HtmlParser($this->_getSetting('mailHtmlStyles'));
            $ret = $p->parse($ret);
        }
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
        $ret = $output->render($this->getData());
        $ret = str_replace('&nbsp;', ' ', $ret);
        $ret = $this->_processPlaceholder($ret, $recipient);
        $ret = $this->getData()->getChildComponent('_redirect')->getComponent()->replaceLinks($ret, $recipient);
        return $ret;
    }

    public function getSubject(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $ret = $this->getRow()->subject;
        $ret = $this->_processPlaceholder($ret, $recipient);
        return $ret;
    }

    protected function _processPlaceholder($ret, Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $plugins = $this->_getSetting('plugins');
        foreach ($plugins as $p) {
            if (is_instance_of($p, 'Vps_Component_Plugin_View_Abstract')) {
                $p = new $p($this->getData()->componentId);
                $ret = $p->processMailOutput($ret, $recipient);
            }
        }
        return $ret;
    }

    public function getPlaceholders(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $ret = array();
        if ($recipient) {
            $ret['firstname'] = $recipient->getMailFirstname();
            $ret['lastname'] = $recipient->getMailLastname();
            if ($recipient instanceof Vpc_Mail_Recipient_TitleInterface) {
                $replace = array(
                    $recipient->getMailTitle(),
                    $recipient->getMailLastname()
                );
                $politeM = trlVps('Dear Mr. {0} {1}', $replace);
                $politeF = trlVps('Dear Mrs. {0} {1}', $replace);
                if ($recipient->getMailGender() == 'male') {
                    $t = trlVps('Dear Mr. {0} {1}', $replace);
                } else if ($recipient->getMailGender() == 'female') {
                    $t = trlVps('Dear Mrs. {0} {1}', $replace);
                } else {
                    $t = trlVps('Dear {0} {1}', $replace);
                }
                $ret['salutation_polite'] = str_replace('  ', ' ', $t);

                if ($recipient->getMailGender() == 'male') {
                    $t = trlVps('Mr. {0}', $recipient->getMailTitle());
                } else if ($recipient->getMailGender() == 'female') {
                    $t = trlVps('Mrs. {0}', $recipient->getMailTitle());
                } else {
                    $t = $recipient->getMailTitle();
                }
                $ret['salutation_title'] = str_replace('  ', ' ', $t);

                $ret['title'] = $recipient->getMailTitle();
            }
            if ($recipient instanceof Vpc_Mail_Recipient_GenderInterface) {
                $replace = array($recipient->getMailLastname());
                if ($recipient->getMailGender() == 'male') {
                    $ret['salutation_polite_notitle'] = trlVps('Dear Mr. {0}', $replace);
                    $ret['salutation_hello'] = trlVps('Hello Mr. {0}', $replace);
                    $ret['salutation'] = trlVps('Mr.');
                } else if ($recipient->getMailGender() == 'female') {
                    $ret['salutation_polite_notitle'] = trlVps('Dear Mrs. {0}', $replace);
                    $ret['salutation_hello'] = trlVps('Hello Mrs. {0}', $replace);
                    $ret['salutation'] = trlVps('Mrs.');
                } else {
                    $replace = array(
                        $recipient->getMailFirstname(),
                        $recipient->getMailLastname()
                    );
                    $ret['salutation_polite_notitle'] = trlVps('Dear {0} {1}', $replace);
                    $ret['salutation_hello'] = trlVps('Hello {0} {1}', $replace);
                }
            }
        }
        return $ret;
    }

    public function addImage(Zend_Mime_Part $image)
    {
        $this->_images[] = $image;
    }
}
