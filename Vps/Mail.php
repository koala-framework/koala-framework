<?php
class Vps_Mail
{
    protected $_mail;
    protected $_view;
    protected $_masterTemplate = null;
    protected $_template;

    public function __construct($template, $masterTemplate = 'Master')
    {
        $this->_view = new Vps_View_Mail();
        if (is_object($template)) {
            if ($template instanceof Vpc_Abstract) {
                $template = $template->getData();
            }
            if (!$template instanceof Vps_Component_Data) {
                throw new Vps_Exception("template must be instance of 'Vpc_Abstract' or 'Vps_Component_Data'");
            }
            $template = $template->componentClass;
        }

        // hier ist $template entweder ein string des templates (zB 'Report', würde in views/mails liegen)
        // oder $template ist ein komponenten-classname. Zuerst wird geprüft, ob das Tpl in views/mails liegt
        $checkTemplate = $template;
        if (substr($checkTemplate, 0, 1) != '/') $checkTemplate = "mails/$checkTemplate";

        if (file_exists($checkTemplate)
            || file_exists("application/views/$checkTemplate.txt.tpl")
            || file_exists("application/views/$checkTemplate.html.tpl")
            || file_exists(VPS_PATH."/views/$checkTemplate.txt.tpl")
            || file_exists(VPS_PATH."/views/$checkTemplate.html.tpl")
        ) {
            $template = $checkTemplate;
        } else {
            $checkTemplate = $template;
            $template = Vpc_Admin::getComponentFile($checkTemplate, 'Component', 'txt.tpl');
            if (!$template) {
                $template = Vpc_Admin::getComponentFile($checkTemplate, 'Component', 'html.tpl');
            }
            $template = str_replace('.txt.tpl', '', $template);
            $template = str_replace('.html.tpl', '', $template);
        }

        $this->_template = $template;
        $this->_masterTemplate = $masterTemplate;

        $this->_mail = new Vps_Mail_Fixed('utf-8');

        $host = $_SERVER['HTTP_HOST'];
        $webUrl = 'http://'.$host;
        $this->_view->webUrl = $webUrl;
        $this->_view->host = $host;
        $this->_view->applicationName = Zend_Registry::get('config')->application->name;
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

    public function getMail()
    {
        return $this->_mail;
    }

    public function getTemplate()
    {
        return $this->_template;
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

    public function addCc($email, $name='')
    {
        if (Zend_Registry::get('config')->debug->sendAllMailsTo) {
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
        if (Zend_Registry::get('config')->debug->sendAllMailsTo) {
            $this->addHeader('X-Real-Bcc', $email);
        } else {
            $this->_mail->addBcc($email);
        }
    }

    public function setReturnPath($email)
    {
        $this->_mail->setReturnPath($email);
    }

    public function addTo($email, $name='')
    {
        if (Zend_Registry::get('config')->debug->sendAllMailsTo) {
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
        $this->_mail->setFrom($email, $name);
    }

    public function send()
    {
        $mailSendAll = Zend_Registry::get('config')->debug->sendAllMailsTo;
        if ($mailSendAll) {
            $this->_mail->addTo($mailSendAll);
        }

        $mailSendAllBcc = Zend_Registry::get('config')->debug->sendAllMailsBcc;
        if ($mailSendAllBcc) {
            $this->_mail->addBcc($mailSendAllBcc);
        }

        if ($this->getFrom() == null) {
            if (Zend_Registry::get('config')->email) {
                $fromName = Zend_Registry::get('config')->email->from->name;
                $fromAddress = Zend_Registry::get('config')->email->from->address;
            } else {
                $hostNonWww = preg_replace('#^www\\.#', '', $_SERVER['HTTP_HOST']);
                $fromName = Zend_Registry::get('config')->application->name;
                $fromAddress = 'noreply@'.$hostNonWww;
            }
            $this->_mail->setFrom($fromAddress, $fromName);
        }

        try {
            $mails = new Vps_Dao_UserMails();
        } catch (Zend_Db_Statement_Exception $e) {
            $vars = array();
            $mails = false;
        }
        if ($mails) {
            $where = array();
            $where['template = ? OR ISNULL(template)'] = $this->_template;
            $vars = $mails->fetchAll($where, 'template');
        }

        $template = "{$this->_template}";

        // txt mail
        $this->_view->setMasterTemplate("mails/{$this->_masterTemplate}.txt.tpl");
        foreach ($vars as $row) {
            $var = $row->variable;
            $this->_view->$var = trim($row->text);
        }
        $this->_mail->setBodyText($this->_view->render("$template.txt.tpl"));

        // html mail
        $this->_view->setMasterTemplate("mails/{$this->_masterTemplate}.html.tpl");
        foreach ($vars as $row) {
            $var = $row->variable;
            $html = $row->html;
            if (trim(strip_tags($html)) == '') $html = '';
            $this->_view->$var = $html;
        }
        $this->_mail->setBodyHtml($this->_view->render("$template.html.tpl"));

        //hinzufügen von Bilder zur Email
        if ($this->_view->getImages()){
            $this->_mail->setType(Zend_Mime::MULTIPART_RELATED);
            foreach ($this->_view->getImages() as $image) {
                $this->_mail->addAttachment($image);
            }
        }
        $this->_mail->setSubject($this->_view->subject);

        return $this->_mail->send();
    }

}
