<?php
class Vps_Mail
{
    protected $_mail;
    protected $_view;
    protected $_masterTemplate = null;
    protected $_template;

    public function __construct($template, $masterTemplate = 'Master')
    {
        $this->_view = new Vps_View();
        $this->_template = $template;
        $this->_masterTemplate = $masterTemplate;

        $this->_mail = new Zend_Mail('utf-8');

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

    public function getView()
    {
        return $this->_view;
    }

    public function getFrom()
    {
        return $this->_mail->getFrom();
    }
    public function addCc($email, $name='')
    {
        $mailSendAll = Zend_Registry::get('config')->debug->sendAllMailsTo;
        if ($mailSendAll) {
            list($mailName, $mailHost) = explode('@', $mailSendAll);
            $this->addHeader('X-Real-Cc-Email', $email);
            $this->addHeader('X-Real-Cc-Name', $name);
            $this->_mail->addCc($mailSendAll, $mailName);
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
        $mailSendAll = Zend_Registry::get('config')->debug->sendAllMailsTo;
        if ($mailSendAll) {
            list($mailName, $mailHost) = explode('@', $mailSendAll);
            $this->addHeader('X-Real-Bcc-Email', $email);
            $this->_mail->addBcc($mailSendAll);
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
        $mailSendAll = Zend_Registry::get('config')->debug->sendAllMailsTo;
        if ($mailSendAll) {
            list($mailName, $mailHost) = explode('@', $mailSendAll);
            $this->addHeader('X-Real-Recipient-Email', $email);
            $this->addHeader('X-Real-Recipient-Name', $name);
            $this->_mail->addTo($mailSendAll, $mailName);
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

        // txt mail
        foreach ($vars as $row) {
            $var = $row->variable;
            $this->_view->$var = trim($row->text);
        }

        $this->_view->setMasterTemplate("mails/{$this->_masterTemplate}.txt.tpl");

        $this->_mail->setBodyText(
            $this->_view->render("mails/{$this->_template}.txt.tpl")
        );

        // html mail
        foreach ($vars as $row) {
            $var = $row->variable;
            $html = $row->html;
            if (trim(strip_tags($html)) == '') $html = '';
            $this->_view->$var = $html;
        }

        $this->_view->setMasterTemplate("mails/{$this->_masterTemplate}.html.tpl");

        $file = "mails/{$this->_template}.html.tpl";
        if (file_exists("application/views/$file") || file_exists(VPS_PATH."/views/$file")) {
            $this->_mail->setBodyHtml( $this->_view->render($file) );
        }

        $this->_mail->setSubject($this->_view->subject);

        return $this->_mail->send();
    }

}
