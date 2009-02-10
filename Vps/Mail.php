<?php
class Vps_Mail
{
    // wozu _ownReturnPath? siehe getReturnPath
    protected $_ownReturnPath = null;

    // die folgenden 4 sind für maillog
    protected $_ownFrom = '';
    protected $_ownTo = array();
    protected $_ownCc = array();
    protected $_ownBcc = array();

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

        if (is_object($template) || (class_exists($template) && is_instance_of($template, 'Vpc_Abstract'))) {
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

            $template = "mails/$template";

            if (!file_exists("application/views/$template.txt.tpl")
                && !file_exists(VPS_PATH."/views/$template.txt.tpl")
            ) {
                throw new Vps_Exception("There has to exist at least a .txt.tpl mail template for '$template'.");
            }
            $this->_txtTemplate = "$template.txt.tpl";
            if (file_exists("application/views/$template.html.tpl")
                || file_exists(VPS_PATH."/views/$template.html.tpl")
            ) {
                $this->_htmlTemplate = "$template.html.tpl";
            }
        }

        $this->_masterTemplate = $masterTemplate;

        $this->_mail = new Vps_Mail_Fixed('utf-8');

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

    public function getReturnPath()
    {
        /**
         * Zend_Mail gibt bei getReturnPath() das From zurück, wenn der return path
         * noch nicht gesetzt wurde, deshalb merken wir uns es selbst.
         */
        return $this->_ownReturnPath;
    }

    public function addCc($email, $name='')
    {
        $this->_ownCc[] = trim("$name <$email>");
        if (Vps_Registry::get('config')->debug->sendAllMailsTo) {
            if ($name) {
                $this->addHeader('X-Real-Cc', $name ." <".$email.">");
            } else {
                $this->addHeader('X-Real-Cc', $email);
            }
        } else {
            $this->_mail->addCc($email, $name);
        }
    }

    public function addHeader($name, $value, $append = false)
    {
        $this->_mail->addHeader($name, $value, $append);
    }

    public function addBcc($email)
    {
        $this->_ownBcc[] = $email;
        if (Vps_Registry::get('config')->debug->sendAllMailsTo) {
            $this->addHeader('X-Real-Bcc', $email);
        } else {
            $this->_mail->addBcc($email);
        }
    }

    public function setReturnPath($email)
    {
        // wozu _ownReturnPath? siehe getReturnPath
        $this->_ownReturnPath = $email;
        $this->_mail->setReturnPath($email);
    }

    public function addTo($email, $name='')
    {
        $this->_ownTo[] = trim("$name <$email>");
        if (Vps_Registry::get('config')->debug->sendAllMailsTo) {
            if ($name) {
                $this->addHeader('X-Real-Recipient', $name ." <".$email.">");
            } else {
                $this->addHeader('X-Real-Recipient', $email);
            }
        } else {
            $this->_mail->addTo($email, $name);
        }
    }

    public function setFrom($email, $name='')
    {
        $this->_ownFrom = trim("$name <$email>");
        $this->_mail->setFrom($email, $name);
    }

    public function send()
    {
        $mailSendAll = Vps_Registry::get('config')->debug->sendAllMailsTo;
        if ($mailSendAll) {
            $this->_mail->addTo($mailSendAll);
        }

        $mailSendAllBcc = Vps_Registry::get('config')->debug->sendAllMailsBcc;
        if ($mailSendAllBcc) {
            $this->_mail->addBcc($mailSendAllBcc);
        }

        $hostNonWww = preg_replace('#^www\\.#', '', $this->_view->host);

        if ($this->getFrom() == null) {
            $fromName = str_replace('%host%', $hostNonWww, Vps_Registry::get('config')->email->from->name);
            $fromAddress = str_replace('%host%', $hostNonWww, Vps_Registry::get('config')->email->from->address);
            $this->setFrom($fromAddress, $fromName);
        }

        if ($this->getReturnPath() == null && Vps_Registry::get('config')->email->returnPath) {
            $returnPath = str_replace('%host%', $hostNonWww, Vps_Registry::get('config')->email->returnPath);
            $this->setReturnPath($returnPath);
        }

        $vars = array();
        if ($this->_mailVarsClassName) {
            try {
                $class = $this->_mailVarsClassName;
                $mails = new $class();
            } catch (Zend_Db_Statement_Exception $e) {
                $mails = false;
            }
            if ($mails) {
                $where = array();
                $where['template = ? OR ISNULL(template)'] = $this->_templateForDbVars;
                $vars = $mails->fetchAll($where, 'template');
            }
        }

        // txt mail
        $this->_view->setMasterTemplate("mails/{$this->_masterTemplate}.txt.tpl");
        foreach ($vars as $row) {
            $var = $row->variable;
            $this->_view->$var = trim($row->text);
        }
        $bodyText = $this->_view->render($this->_txtTemplate);
        $this->_mail->setBodyText($bodyText);

        // html mail
        $bodyHtml = null;
        if ($this->_htmlTemplate) {
            $this->_view->setMasterTemplate("mails/{$this->_masterTemplate}.html.tpl");
            foreach ($vars as $row) {
                $var = $row->variable;
                $html = $row->html;
                if (trim(strip_tags($html)) == '') $html = '';
                $this->_view->$var = $html;
            }
            $bodyHtml = $this->_view->render($this->_htmlTemplate);
            $this->_mail->setBodyHtml($bodyHtml);
        }

        //hinzufügen von Bilder zur Email
        if ($this->_view->getImages()){
            $this->_mail->setType(Zend_Mime::MULTIPART_RELATED);
            foreach ($this->_view->getImages() as $image) {
                $this->_mail->addAttachment($image);
            }
        }
        $this->_mail->setSubject($this->_view->subject);

        // eigenen transport setzen, damit returnPath korrekt funktioniert
        $transport = null;
        if ($this->getReturnPath() != null) {
            $transport = new Zend_Mail_Transport_Sendmail('-f'.$this->getReturnPath());
        }

        // in service mitloggen wenn url vorhanden
        if (Vps_Util_Model_MailLog::isAvailable()) {
            $r = Vps_Model_Abstract::getInstance('Vps_Util_Model_MailLog')->createRow();
            if (isset($_COOKIE['unitTest']) && $_COOKIE['unitTest']) {
                $r->identifier = $_COOKIE['unitTest'];
            }
            $r->from = $this->_ownFrom;
            $r->to = implode(';', $this->_ownTo);
            $r->cc = implode(';', $this->_ownCc);
            $r->bcc = implode(';', $this->_ownBcc);
            if ($this->getReturnPath() != null) {
                $r->return_path = $this->getReturnPath();
            }
            $r->subject = $this->_mail->getSubject();
            $r->body_text = $bodyText;
            $r->body_html = $bodyHtml;
            $r->save();
        }

        return $this->_mail->send($transport);
    }

}
