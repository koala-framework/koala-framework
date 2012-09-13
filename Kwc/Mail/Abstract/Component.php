<?php
/**
 * Used for sending mails with coded subject, from etc
 *
 * doesn't need a row and isn't editable
 */
abstract class Kwc_Mail_Abstract_Component extends Kwc_Abstract
{
    private $_mailData;
    protected $_images = array();

    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['redirect'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Mail_Redirect_Component',
            'name' => trlKwfStatic('E-Mail'),
            'filename' => 'r'
        );

        $ret['viewCache'] = false;

        $ret['mailHtmlStyles'] = array();
        $ret['plugins']['placeholders'] = 'Kwc_Mail_PlaceholdersPlugin';

        // set shorter source keys for recipient models
        // key = sourceShortcut, value = modelClass
        // e.g. array('user' => 'Kwf_User_Model')
        $ret['recipientSources'] = array();

        $ret['fromName'] = null;
        $ret['fromEmail'] = null;
        $ret['replyEmail'] = null;
        $ret['bcc'] = null;
        $ret['returnPath'] = null;
        $ret['subject'] = trlKwf('Automatically sent e-mail');
        $ret['attachImages'] = false;

        return $ret;
    }

    public function getHtmlStyles()
    {
        $ret = $this->_getSetting('mailHtmlStyles');
        return $ret;
    }

    public function createMail(Kwc_Mail_Recipient_Interface $recipient, $data = null, $toAddress = null, $format = null)
    {
        $this->_images = array();
        $this->_mailData = $data;

        $mail = new Kwf_Mail();
        $name = $recipient->getMailFirstname() . ' ' . $recipient->getMailLastname();
        if (!$name == ' ') $name = null;
        if ($toAddress) {
            $mail->addTo($toAddress, $name);
        } else {
            $mail->addTo($recipient->getMailEmail(), $name);
        }

        if ((!$format && $recipient->getMailFormat() == Kwc_Mail_Recipient_Interface::MAIL_FORMAT_HTML) ||
            $format == Kwc_Mail_Recipient_Interface::MAIL_FORMAT_HTML)
        {
            $mail->setBodyHtml($this->getHtml($recipient, true));
        }
        $mail->setBodyText($this->getText($recipient));
        $mail->setSubject($this->getSubject($recipient));
        if ($this->_getSetting('fromEmail')) {
            $mail->setFrom($this->_getSetting('fromEmail'), $this->_getSetting('fromName'));
        }
        if ($this->_getSetting('replyEmail')) {
            $mail->setReplyTo($this->_getSetting('replyEmail'));
        }
        if ($this->_getSetting('returnPath')) {
            $mail->setReturnPath($this->_getSetting('returnPath'));
        }

        if ($this->_images) {
            $mail->setType(Zend_Mime::MULTIPART_RELATED);
            foreach ($this->_images as $image) {
                $mail->addAttachment($image);
            }
        }

        $bccs = $this->_getSetting('bcc');
        if ($bccs) {
            if (!is_array($bccs)) $bccs = array($bccs);
            foreach ($bccs as $bcc) {
                $mail->addBcc($bcc);
            }
        }
        //TODO: attachments

        return $mail;
    }

    /**
     * Verschickt ein mail an @param $recipient.
     * @param $data Optionale Daten die benötigt werden, kann von den
     *        Komponenten per $this->getData()->getParentByClass('Kwc_Mail_Component')->getComponent()->getMailData();
     *        ausgelesen werden
     * Wird von Gästebuch verwendet
     */
    public function send(Kwc_Mail_Recipient_Interface $recipient, $data = null, $toAddress = null, $format = null)
    {
        $mail = $this->createMail($recipient, $data, $toAddress, $format);
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
     * @param bool attachImages: ob images als attachment angehängt werden sollen oder nicht
     *                           (needs to be set to false even if attachImages setting is true
     *                           when createing the html preview in the backend)
     */
    public function getHtml(Kwc_Mail_Recipient_Interface $recipient = null, $attachImages = false)
    {
        $renderer = new Kwf_Component_Renderer_Mail();
        $renderer->setRenderFormat(Kwf_Component_Renderer_Mail::RENDER_HTML);
        $renderer->setRecipient($recipient);
        if ($this->_getSetting('attachImages')) {
            $renderer->setAttachImages($attachImages);
        }
        $ret = $renderer->renderComponent($this->getData());
        $ret = $this->_processPlaceholder($ret, $recipient);
        $ret = $this->getData()->getChildComponent('_redirect')->getComponent()->replaceLinks($ret, $recipient);
        $htmlStyles = $this->getHtmlStyles();
        if ($htmlStyles){
            $p = new Kwc_Mail_HtmlParser($htmlStyles);
            $ret = $p->parse($ret);
        }
        return $ret;
    }

    /**
     * Gibt den personalisierten Quelltext der Mail zurück
     *
     * @see getHtml Für Ersetzungen siehe
     */
    public function getText(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        $renderer = new Kwf_Component_Renderer_Mail();
        $renderer->setRenderFormat(Kwf_Component_Renderer_Mail::RENDER_TXT);
        $renderer->setRecipient($recipient);
        $ret = $renderer->renderComponent($this->getData());
        $ret = $this->_processPlaceholder($ret, $recipient);
        $ret = str_replace('&nbsp;', ' ', $ret);
        $ret = $this->getData()->getChildComponent('_redirect')->getComponent()->replaceLinks($ret, $recipient);
        return $ret;
    }

    protected function _getSubject()
    {
        return $this->_getSetting('subject');
    }

    public function getSubject(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        $ret = $this->_getSubject();
        $ret = $this->_processPlaceholder($ret, $recipient);
        return $ret;
    }

    protected function _processPlaceholder($ret, Kwc_Mail_Recipient_Interface $recipient = null)
    {
        //replace special unicode chars, causes problems in Lotus Notes
        $ret = str_replace(chr(0xE2).chr(0x80).chr(0x8B), '', $ret); //zero width space
        $ret = str_replace('–', '-', $ret);

        $plugins = $this->_getSetting('plugins');
        foreach ($plugins as $p) {
            if (is_instance_of($p, 'Kwf_Component_Plugin_View_Abstract')) {
                $p = new $p($this->getData()->componentId);
                $ret = $p->processMailOutput($ret, $recipient);
            }
        }
        return $ret;
    }

    public function getPlaceholders(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        $ret = array();
        if ($recipient) {
            $ret['firstname'] = $recipient->getMailFirstname();
            $ret['lastname'] = $recipient->getMailLastname();
            if ($recipient instanceof Kwc_Mail_Recipient_TitleInterface) {
                $replace = array(
                    $recipient->getMailTitle(),
                    $recipient->getMailLastname()
                );
                $politeM = $this->getData()->trlKwf('Dear Mr. {0} {1}', $replace);
                $politeF = $this->getData()->trlKwf('Dear Mrs. {0} {1}', $replace);
                if ($recipient->getMailGender() == 'male' && $recipient->getMailLastname()) {
                    $t = $this->getData()->trlKwf('Dear Mr. {0} {1}', $replace);
                } else if ($recipient->getMailGender() == 'female' && $recipient->getMailLastname()) {
                    $t = $this->getData()->trlKwf('Dear Mrs. {0} {1}', $replace);
                } else {
                    $t = $this->getData()->trlKwf('Dear Sir or Madam');
                }
                $ret['salutation_polite'] = trim(str_replace('  ', ' ', $t));

                if ($recipient->getMailGender() == 'male') {
                    $t = $this->getData()->trlKwf('Mr. {0}', $recipient->getMailTitle());
                } else if ($recipient->getMailGender() == 'female') {
                    $t = $this->getData()->trlKwf('Mrs. {0}', $recipient->getMailTitle());
                } else {
                    $t = $recipient->getMailTitle();
                }
                $ret['salutation_title'] = trim(str_replace('  ', ' ', $t));

                $ret['title'] = $recipient->getMailTitle();
            }
            if ($recipient instanceof Kwc_Mail_Recipient_GenderInterface) {
                $replace = array($recipient->getMailLastname());
                if ($recipient->getMailGender() == 'male') {
                    $ret['salutation_polite_notitle'] = $this->getData()->trlKwf('Dear Mr. {0}', $replace);
                    $ret['salutation_hello'] = $this->getData()->trlKwf('Hello Mr. {0}', $replace);
                    $ret['salutation'] = $this->getData()->trlKwf('Mr.');
                    $ret['salutation_firstname'] = $this->getData()->trlcKwf('salutation firstname male', 'Dear {0}', array($recipient->getMailFirstname()));
                } else if ($recipient->getMailGender() == 'female') {
                    $ret['salutation_polite_notitle'] = $this->getData()->trlKwf('Dear Mrs. {0}', $replace);
                    $ret['salutation_hello'] = $this->getData()->trlKwf('Hello Mrs. {0}', $replace);
                    $ret['salutation'] = $this->getData()->trlKwf('Mrs.');
                    $ret['salutation_firstname'] = $this->getData()->trlcKwf('salutation firstname female', 'Dear {0}', array($recipient->getMailFirstname()));
                } else {
                    $replace = array(
                        $recipient->getMailFirstname(),
                        $recipient->getMailLastname()
                    );
                    if ($recipient->getMailFirstname() && $recipient->getMailLastname()) {
                        $ret['salutation_polite_notitle'] = trim($this->getData()->trlKwf('Dear {0} {1}', $replace));
                    } else {
                        $ret['salutation_polite_notitle'] = $this->getData()->trlKwf('Dear Sir or Madam');
                    }
                    $ret['salutation_hello'] = trim($this->getData()->trlKwf('Hello {0} {1}', $replace));
                    $ret['salutation_firstname'] = $this->getData()->trlcKwf('salutation firstname unknown gender', 'Dear {0}', array($recipient->getMailFirstname()));
                }
            }
        }
        return $ret;
    }

    public function addImage(Zend_Mime_Part $image)
    {
        // Bild nur hinzufügen wenn dasselbe nicht bereits hinzugefügt wurde.
        // wenns das bild schon gibt, hat es eh die gleiche cid
        $found = false;
        foreach ($this->_images as $addedImg) {
            if ($image == $addedImg) $found = true;
        }
        if ($found === false) $this->_images[] = $image;
    }
}
