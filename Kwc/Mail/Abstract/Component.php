<?php
/**
 * Used for sending mails with coded subject, from etc
 *
 * doesn't need a row and isn't editable
 */
abstract class Kwc_Mail_Abstract_Component extends Kwc_Abstract
    implements Kwf_Media_Output_Interface
{
    private $_mailData;

    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['redirect'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Mail_Redirect_Component',
            'name' => trlKwfStatic('E-Mail'),
            'filename' => 'r'
        );

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
        $ret['trackViews'] = false;

        return $ret;
    }

    //override for dynamic recipient sources
    public function getRecipientSources()
    {
        return $this->_getSetting('recipientSources');
    }

    public function getHtmlStyles()
    {
        $ret = $this->_getSetting('mailHtmlStyles');
        return $ret;
    }

    public function createMail(Kwc_Mail_Recipient_Interface $recipient, $data = null, $toAddress = null, $format = null, $addViewTracker = true)
    {
        $this->_mailData = $data;

        $mail = new Kwf_Mail();
        $name = $recipient->getMailFirstname() . ' ' . $recipient->getMailLastname();
        if (!$recipient->getMailFirstname() || !$recipient->getMailLastname()) {
            //no name at all if we don't have a complete name
            $name = null;
        }
        if ($toAddress) {
            $mail->addTo($toAddress, $name);
        } else {
            $mail->addTo($recipient->getMailEmail(), $name);
        }

        if ((!$format && $recipient->getMailFormat() == Kwc_Mail_Recipient_Interface::MAIL_FORMAT_HTML) ||
            $format == Kwc_Mail_Recipient_Interface::MAIL_FORMAT_HTML)
        {
            $html = $this->getHtml($recipient, $addViewTracker);
            $mail->setDomain($this->getData()->getDomain());
            $mail->setAttachImages($this->_getSetting('attachImages'));
            $mail->setBodyHtml($html);
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

        $bccs = $this->_getSetting('bcc');
        if ($bccs) {
            if (!is_array($bccs)) $bccs = array($bccs);
            foreach ($bccs as $bcc) {
                $mail->addBcc($bcc);
            }
        }

        return $mail;
    }

    /**
     * Verschickt ein mail an @param $recipient.
     * @param $data Optionale Daten die benötigt werden, kann von den
     *        Komponenten per $this->getData()->getParentByClass('Kwc_Mail_Component')->getComponent()->getMailData();
     *        ausgelesen werden
     * Wird von Gästebuch verwendet
     */
    public function send(Kwc_Mail_Recipient_Interface $recipient, $data = null, $toAddress = null, $format = null, $addViewTracker = true)
    {
        $mail = $this->createMail($recipient, $data, $toAddress, $format, $addViewTracker);
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
     */
    public function getHtml(Kwc_Mail_Recipient_Interface $recipient = null, $addViewTracker = false)
    {
        $renderer = new Kwf_Component_Renderer_Mail();
        $renderer->setRenderFormat(Kwf_Component_Renderer_Mail::RENDER_HTML);
        $renderer->setRecipient($recipient);
        $renderer->setHtmlStyles($this->getHtmlStyles());
        $ret = $renderer->renderComponent($this->getData());
        Kwf_Benchmark::checkpoint('html: render');
        $ret = $this->_processPlaceholder($ret, $recipient);
        Kwf_Benchmark::checkpoint('html: placeholder');
        $redirectComponent = $this->getData()->getChildComponent('_redirect')->getComponent();
        $ret = $redirectComponent->replaceLinks($ret, $recipient);
        Kwf_Benchmark::checkpoint('html: replaceLinks');
        if ($addViewTracker && $this->_getSetting('trackViews')) {
            $params = array();
            if ($recipient->id) $params['recipientId'] = urlencode($recipient->id);
            if ($shortcut = $redirectComponent->getRecipientModelShortcut(get_class($recipient->getModel())))
                $params['recipientModelShortcut'] = urlencode($shortcut);
            $https = Kwf_Util_Https::domainSupportsHttps($this->getData()->getDomain());
            $protocol = $https ? 'https' : 'http';
            $imgUrl = $protocol . '://'.$this->getData()->getDomain() .
                Kwf_Media::getUrl($this->getData()->componentClass, $this->getData()->componentId,
                    'views', 'blank.gif');
            $imgUrl .= '?' . http_build_query($params);
            $ret .= '<img src="' . $imgUrl . '" width="1" height="1" />';
            Kwf_Benchmark::checkpoint('html: view tracker');
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
        Kwf_Benchmark::checkpoint('text: render');
        $ret = $this->_processPlaceholder($ret, $recipient);
        Kwf_Benchmark::checkpoint('text: placeholder');
        $ret = str_replace('&nbsp;', ' ', $ret);
        $ret = $this->getData()->getChildComponent('_redirect')->getComponent()->replaceLinks($ret, $recipient);
        Kwf_Benchmark::checkpoint('text: replaceLinks');
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
            if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewAfterChildRender')) {
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
            $ret = Kwc_Mail_Recipient_Placeholders::getPlaceholders($recipient, $this->getData()->getLanguage());
        }
        return $ret;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        if ($type == 'views') {
            if (isset($_GET['recipientId']) && $_GET['recipientId'] &&
                isset($_GET['recipientModelShortcut']) && $_GET['recipientModelShortcut']) {
                $model = Kwf_Model_Abstract::getInstance('Kwc_Mail_Abstract_ViewsModel');
                $model->createRow(array(
                    'mail_component_id' => $id,
                    'recipient_id' => $_GET['recipientId'],
                    'recipient_model_shortcut' => $_GET['recipientModelShortcut'],
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'date' => date('Y-m-d H:i:s')
                ))->save();
            }
            $file = array(
                'contents' => base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='),
                'mimeType' => 'image/gif',
                'lifetime' => false
            );
            return $file;
        }
    }
}
