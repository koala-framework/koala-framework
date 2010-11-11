<?php
class Vps_Mail_Template implements Vps_Mail_Interface
{
    protected $_mail;
    protected $_view;
    protected $_masterTemplate = null;
    protected $_mailVarsClassName = 'Vps_Dao_UserMails';
    protected $_txtTemplate;
    protected $_htmlTemplate;
    protected $_templateForDbVars;

    public function __construct($template, $masterTemplate = 'Master')
    {
        $this->_view = new Vps_View_Mail();

        // das substr mit Vpc_ muss sein weil auf prosalzburg test server sonst nur eine weiße seite kommt
        if (is_object($template) || ((substr($template, 0, 4) == 'Vpc_' || substr($template, 0, 4) == 'Vps_')
            && class_exists($template) && is_instance_of($template, 'Vpc_Abstract'))
        ) {
            if (is_object($template)) {
                if ($template instanceof Vpc_Abstract) {
                    $template = $template->getData();
                }
                if (!$template instanceof Vps_Component_Data) {
                    throw new Vps_Exception("template must be instance of 'Vpc_Abstract' or 'Vps_Component_Data'");
                }
                $template = $template->componentClass;
            }
            $this->_templateForDbVars = $template;

            $this->_txtTemplate = Vpc_Admin::getComponentFile($template, 'Component', 'txt.tpl');
            if (!$this->_txtTemplate) {
                throw new Vps_Exception("Component class '$template' needs at least a .txt.tpl mail template.");
            }
            $this->_htmlTemplate = Vpc_Admin::getComponentFile($template, 'Component', 'html.tpl');
        } else {
            if (substr($template, 0, 1) == '/') {
                throw new Vps_Exception("Absolute mail template paths are not allowed. You called '$template'.");
            }
            $this->_templateForDbVars = $template;

            if (false === $this->_view->getScriptPath("$template.txt.tpl")) {
                $template = "mails/$template";
                if (false === $this->_view->getScriptPath("$template.txt.tpl")) {
                    throw new Vps_Exception("There has to exist at least a .txt.tpl mail template for '$template'.");
                }
            }
            $this->_txtTemplate = "$template.txt.tpl";

            if (false !== $this->_view->getScriptPath("$template.html.tpl")) {
                $this->_htmlTemplate = "$template.html.tpl";
            }
        }

        $this->_masterTemplate = $masterTemplate;

        $this->_mail = new Vps_Mail();

        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Vps_Registry::get('config')->server->domain;
        }
        $this->_view->webUrl = 'http://'.$host;
        $this->_view->host = $host;

        $this->_view->applicationName = Vps_Registry::get('config')->application->name;
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

    public function setMailVarsClassName($name)
    {
        $this->_mailVarsClassName = $name;
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
        return $this->_txtTemplate;
    }

    public function getHtmlTemplate()
    {
        return $this->_htmlTemplate;
    }

    public function getTemplateForDbVars()
    {
        return $this->_templateForDbVars;
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

    public function addAttachment(Zend_Mime_Part $attachment)
    {
        $this->_mail->addAttachment($attachment);
        return $this;
    }

    public function setBodyText($text)
    {
        throw new Vps_Exception("Text body may not be set manual when using 'Vps_Mail_Template', "
            ."because it is automatically build by a template.");
    }

    public function setBodyHtml($html)
    {
        throw new Vps_Exception("Html body may not be set manual when using 'Vps_Mail_Template', "
            ."because it is automatically build by a template.");
    }

    // constants for type defined in Vps_Model_Mail_Row
    public function getMailContent($type = Vps_Model_Mail_Row::GET_MAIL_CONTENT_AUTO)
    {
        if ($type == Vps_Model_Mail_Row::GET_MAIL_CONTENT_AUTO) {
            $ret = $this->_getHtmlMailContent();
            if (is_null($ret)) $ret = $this->_getTextMailContent();
            return $ret;
        } else if ($type == Vps_Model_Mail_Row::GET_MAIL_CONTENT_HTML) {
            return $this->_getHtmlMailContent();
        } else if ($type == Vps_Model_Mail_Row::GET_MAIL_CONTENT_TEXT) {
            return $this->_getTextMailContent();
        }

        return null;
    }

    private function _getVars()
    {
        $vars = array();
        if ($this->_mailVarsClassName) {
            try {
                // TODO: Kann wohl weg, das war proHaustier dynamische Mails
                $class = $this->_mailVarsClassName;
                $mails = new $class();
                if ($mails) {
                    $where = array();
                    $where['template = ? OR ISNULL(template)'] = $this->_templateForDbVars;
                    $vars = $mails->fetchAll($where, 'template');
                }
            } catch (Zend_Db_Statement_Exception $e) {
                $vars = false;
            }
        }
        if (!$vars) $vars = array();
        return $vars;
    }

    private function _getTextMailContent()
    {
        $vars = $this->_getVars();

        foreach ($vars as $row) {
            $var = $row->variable;
            $this->_view->$var = trim($row->text);
        }
        return $this->_view->render($this->_txtTemplate);
    }

    private function _getHtmlMailContent()
    {
        $bodyHtml = null;
        if ($this->_htmlTemplate) {
            $vars = $this->_getVars();

            foreach ($vars as $row) {
                $var = $row->variable;
                $html = $row->html;
                if (trim(strip_tags($html)) == '') $html = '';
                $this->_view->$var = $html;
            }
            $bodyHtml = $this->_view->render($this->_htmlTemplate);
        }
        return $bodyHtml;
    }

    public function send()
    {
        // txt mail
        $this->_view->setMasterTemplate("mails/{$this->_masterTemplate}.txt.tpl");
        $this->_mail->setBodyText($this->_getTextMailContent());

        // html mail
        $bodyHtml = $this->_getHtmlMailContent();
        if (!is_null($bodyHtml)) {
            $this->_view->setMasterTemplate("mails/{$this->_masterTemplate}.html.tpl");
            $this->_mail->setBodyHtml($bodyHtml);
        }

        //hinzufügen von Bilder zur Email
        if ($this->_view->getImages()) {
            $this->_mail->setType(Zend_Mime::MULTIPART_RELATED);
            $addedImages = array();
            foreach ($this->_view->getImages() as $image) {
                if (in_array($image, $addedImages)) continue;

                $this->_mail->addAttachment($image);
                $addedImages[] = $image;
            }
        }

        if (!($this->getSubject()) && $this->_view->subject) {
            $this->setSubject($this->_view->subject);
        }

        return $this->_mail->send();
    }

}
