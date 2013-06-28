<?php
class Kwf_Mail_Template
{
    protected $_mail;
    protected $_view;
    protected $_masterTemplate = null;
    protected $_txtTemplate;
    protected $_htmlTemplate;

    /**
    * constructing a Kwf_Mail_Template_View and a Kwf_Mail object.
    * @param string $template see Kwf_Mail_Template_View
    * @param string $masterTemplate see Kwf_Mail_Template_View
    */
    public function __construct($template, $masterTemplate = 'Master')
    {
        $this->_view = new Kwf_Mail_Template_View($template, $masterTemplate);
        $this->_mail = new Kwf_Mail();
    }

    public function __set($key, $val)
    {
        $this->_view->$key = $val;
    }

    public function __get($key)
    {
        return $this->_view->$key;
    }

    public function __unset($key)
    {
        unset($this->_view->$key);
    }

    public function __isset($key)
    {
        return isset($this->_view->$key);
    }

    public function assign($spec, $value = null)
    {
        return $this->_view->assign($spec, $value);
    }

    public function setMail($mail)
    {
        $this->_mail = $mail;
    }

    public function getMail()
    {
        return $this->_mail;
    }

    public function getTxtTemplate()
    {
        return $this->_view->getTxtTemplate();
    }

    public function getHtmlTemplate()
    {
        return $this->_view->getHtmlTemplate();
    }

    public function getView()
    {
        return $this->_view;
    }

    public function getFrom()
    {
        return $this->_mail->getFrom();
    }

    public function getRecipients()
    {
        return $this->_mail->getRecipients();
    }

    public function getHeaders()
    {
        return $this->_mail->getHeaders();
    }

    public function getSubject()
    {
        return $this->_mail->getSubject();
    }

    public function getReturnPath()
    {
        return $this->_mail->getReturnPath();
    }

    public function setSubject($subject)
    {
        $this->_mail->setSubject($subject);
    }

    public function addCc($email, $name='')
    {
        $this->_mail->addCc($email, $name);
        return $this;
    }

    public function addHeader($name, $value, $append = false)
    {
        $this->_mail->addHeader($name, $value, $append);
        return $this;
    }

    public function addBcc($email)
    {
        $this->_mail->addBcc($email);
        return $this;
    }

    public function addTo($email, $name='')
    {
        $this->_mail->addTo($email, $name);
        return $this;
    }

    public function setFrom($email, $name='')
    {
        $this->_mail->setFrom($email, $name);
        return $this;
    }

    public function setReturnPath($email)
    {
        $this->_mail->setReturnPath($email);
        return $this;
    }

    public function setAttachImages($attachImages)
    {
        $this->_mail->setAttachImages($attachImages);
        return $this;
    }

    public function addAttachment(Zend_Mime_Part $attachment)
    {
        $this->_mail->addAttachment($attachment);
        return $this;
    }

    public function setBodyText($text)
    {
        throw new Kwf_Exception("Text body may not be set manual when using 'Kwf_Mail_Template', "
            ."because it is automatically build by a template.");
    }

    public function setBodyHtml($html)
    {
        throw new Kwf_Exception("Html body may not be set manual when using 'Kwf_Mail_Template', "
            ."because it is automatically build by a template.");
    }

    // constants for type defined in Kwf_Model_Mail_Row
    public function getMailContent($type = Kwf_Model_Mail_Row::MAIL_CONTENT_AUTO)
    {
        if ($type == Kwf_Model_Mail_Row::MAIL_CONTENT_AUTO) {
            $ret = $this->_getHtmlMailContent();
            if (is_null($ret)) $ret = $this->_getTextMailContent();
            return $ret;
        } else if ($type == Kwf_Model_Mail_Row::MAIL_CONTENT_HTML) {
            return $this->_getHtmlMailContent();
        } else if ($type == Kwf_Model_Mail_Row::MAIL_CONTENT_TEXT) {
            return $this->_getTextMailContent();
        }

        return null;
    }

    protected function _getTextMailContent()
    {
        return $this->_view->renderText();
    }

    protected function _getHtmlMailContent()
    {
        return $this->_view->renderHtml();
    }

    public function send()
    {
        // txt mail
        $this->_mail->setBodyText($this->_getTextMailContent());

        // html mail
        $bodyHtml = $this->_getHtmlMailContent();
        if ($bodyHtml) {
            $this->_mail->setBodyHtml($bodyHtml);
        }

        if (!($this->getSubject()) && $this->_view->subject) {
            $this->setSubject($this->_view->subject);
        }

        return $this->_mail->send();
    }
}
