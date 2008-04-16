<?php
class Vps_Mail
{
    protected $_mail;
    protected $_view;
    protected $_renderFile;
    protected $_template;

    public function __construct($template, $renderFile = 'Master')
    {
        $this->_view = new Vps_View_Mail_Smarty();
        $this->_template = $template;
        $this->_renderFile = $renderFile;

        $this->_mail = new Zend_Mail('utf-8');

        $webUrl = 'http://'.$_SERVER['HTTP_HOST'];
        $host = $_SERVER['HTTP_HOST'];
        $this->_view->webUrl = $webUrl;
        $this->_view->host = $host;
        $this->_view->applicationName = Zend_Registry::get('config')->application->name;

        if (Zend_Registry::get('config')->email) {
            $fromName = Zend_Registry::get('config')->email->from->name;
            $fromAddress = Zend_Registry::get('config')->email->from->address;
        } else {
            $hostNonWww = preg_replace('#^www\\.#', '', $host);
            $fromName = Zend_Registry::get('config')->application->name;
            $fromAddress = 'noreply@'.$hostNonWww;
        }
        $this->_mail->setFrom($fromName, $fromAddress);

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
        isset($this->_view->$key);
    }

    public function assign($spec, $value = null)
    {
        return $this->view->assign($spec, $value);
    }

    public function getMail()
    {
        return $this->_mail;
    }
    public function getView()
    {
        return $this->_view;
    }

    public function addCc($email, $name='')
    {
        $this->_mail->addCC($email, $name);
    }
    public function addBcc($email)
    {
        $this->_mail->addBcc($email);
    }
    public function setReturnPath($email)
    {
        $this->_mail->setReturnPath($email);
    }
    public function addTo($email, $name='')
    {
        $this->_mail->addTo($email, $name);
    }
    public function setFrom($email, $name='')
    {
        $this->_mail->setForm($email, $name);
    }
    public function send()
    {
        try {
            $mails = new Vps_Dao_UserMails();
        } catch (Zend_Db_Statement_Exception $e) {
            $vars = array();
            $mails = false;
        }
        if ($mails) {
            $where = array();
            $where['template = ? OR ISNULL(template)'] = $this->_template;
            $vars = $mails->fetchAll($where);
        }

        foreach ($vars as $row) {
            $var = $row->variable;
            $this->_view->$var = $row->text;
        }

        if (file_exists("application/views/mails/{$this->_renderFile}.txt.tpl")) {
            $file = "mails/{$this->_renderFile}.txt.tpl";
        } else {
            $file = VPS_PATH."/views/mails/{$this->_renderFile}.txt.tpl";
        }
        $this->_view->setRenderFile($file);

        if (file_exists("application/views/mails/{$this->_template}.txt.tpl")) {
            $file = "mails/{$this->_template}.txt.tpl";
        } else {
            $file = VPS_PATH."/views/mails/{$this->_template}.txt.tpl";
        }

        $c = $this->_view->render($file);
        $this->_mail->setBodyText($c);

        foreach ($vars as $row) {
            $var = $row->variable;
            $this->_view->$var = $row->html;
        }

        if (file_exists("application/views/mails/{$this->_renderFile}.html.tpl")) {
            $file = "mails/{$this->_renderFile}.html.tpl";
        } else {
            $file = VPS_PATH."/views/mails/{$this->_renderFile}.html.tpl";
        }
        $this->_view->setRenderFile($file);

        if (file_exists("application/views/mails/{$this->_template}.html.tpl")) {
            $file = "mails/{$this->_template}.html.tpl";
        } else {
            $file = VPS_PATH."/views/mails/{$this->_template}.html.tpl";
            if (!file_exists($file)) $file = '';
        }
        if ($file) {
            $c = $this->_view->render($file);
            $this->_mail->setBodyHtml($c);
        }

        $this->_mail->setSubject($this->_view->subject);

        return $this->_mail->send();
    }

}
