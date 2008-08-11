<?php
class Vps_Model_Mail_Row extends Vps_Model_Db_Row
{
    protected $_mail;
    private $_mailData = array();

    public function __construct(array $config)
    {
        parent::__construct($config);

        if (!empty($this->template)) {
            $tpl = $this->template;
        } else {
            $tpl = $this->getModel()->getMailTemplate();
        }
        if (!$tpl) {
            throw new Vps_Exception("Mail template not set for model '".get_class($this->getModel())."'");
        }

        $this->_mail = new Vps_Mail($tpl);
    }

    public function sendMail()
    {
        if ($this->mail_sent || $this->is_spam) return;

        $data = $this->serializedData;
        foreach ($data as $k => $v) {
            $this->_mail->$k = $v;
        }

        $mail->subject = $data['subject'];

        $mailData = $data['mailSendData'];
        if (isset($mailData['cc'])) {
            foreach ($mailData['cc'] as $v) {
                $this->_mail->addCc($v['email'], $v['name']);
            }
        }
        if (isset($mailData['header'])) {
            foreach ($mailData['header'] as $v) {
                $this->_mail->addHeader($v['name'], $v['value'], $v['append']);
            }
        }
        if (isset($mailData['bcc'])) {
            foreach ($mailData['bcc'] as $v) {
                $this->_mail->addBcc($v['email']);
            }
        }
        if (isset($mailData['to'])) {
            foreach ($mailData['to'] as $v) {
                $this->_mail->addTo($v['email'], $v['name']);
            }
        }
        if (isset($mailData['returnPath'])) {
            $this->_mail->setReturnPath($mailData['returnPath']['email']);
        }
        if (isset($mailData['from'])) {
            $this->_mail->setFrom($mailData['from']['email'], $mailData['from']['name']);
        }

        $this->_mail->send();

        $this->mail_sent = 1;
        $this->save();
    }

    private function _insert()
    {
        $data = $this->serializedData;
        $data['mailSendData'] = $this->_mailData;

        $this->save_date = date('Y-m-d H:i:s');
        $this->is_spam = 0;
        $this->mail_sent = 0;
        $this->template = $this->_mail->getTemplate();
        $this->serializedData = $data;
    }

    public function save()
    {
        if (empty($this->id)) {
            $this->_insert();
            parent::save();
            $this->_checkSpam();
        } else {
            parent::save();
        }
        $this->sendMail();
    }

    public function __get($columnName)
    {
        if (!$this->_row->__isset($columnName)) {
            $data = $this->serializedData;
            return !empty($data[$columnName]) ? $data[$columnName] : '';
        }
        return parent::__get($columnName);
    }

    public function __isset($columnName)
    {
        return true;
    }

    public function __set($columnName, $value)
    {
        if (!$this->_row->__isset($columnName)) {
            $data = $this->serializedData;
            $data[$columnName] = $value;
            $this->serializedData = $data;
        } else {
            parent::__set($columnName, $value);
        }
    }

    public function getSpamKey()
    {
        return substr(md5($this->email), 0, 15);
    }

    private function _checkSpam()
    {
        $spamFields = $this->getModel()->getSpamFields();
        if (!$spamFields || $this->is_spam) return;

        $additionalData = array(
            'http_host' => $_SERVER['HTTP_HOST'],
            'ham_url' => '/vps/spam/set?id='.$this->id.'&value=0&key='.$this->getSpamKey()
        );

        require_once "HTTP/Request.php";

        $text = array();
        foreach ($spamFields as $v) {
            $text[] = $this->$v;
        }
        $text = implode("\n", $text);

        $req = new HTTP_Request('http://cms.vivid-planet.com/spamfilter/check.php?method=checkSpam');
        $req->setMethod(HTTP_REQUEST_METHOD_POST);
        $req->addPostData('text', (strlen($text) > 2000 ? substr($text, 0, 2000) : $text));
        $req->addPostData('additional_data', $additionalData);
        $res = $req->sendRequest();
        if ($res) {
            $this->is_spam = $req->getResponseBody();
            $this->save();
        }
    }

    public function getMail()
    {
        return $this->_mail;
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

    public function addCc($email, $name = '')
    {
        if (!isset($this->_mailData['cc'])) $this->_mailData['cc'] = array();
        $this->_mailData['cc'][] = array('email' => $email, 'name' => $name);
    }

    public function addHeader($name, $value, $append = false)
    {
        if (!isset($this->_mailData['header'])) $this->_mailData['header'] = array();
        $this->_mailData['header'][] = array('name' => $name, 'value' => $value, 'append' => $append);
    }

    public function addBcc($email)
    {
        if (!isset($this->_mailData['bcc'])) $this->_mailData['bcc'] = array();
        $this->_mailData['bcc'][] = array('email' => $email);
    }

    public function setReturnPath($email)
    {
        $this->_mailData['returnPath'] = array('email' => $email);
    }

    public function addTo($email, $name = '')
    {
        if (!isset($this->_mailData['to'])) $this->_mailData['to'] = array();
        $this->_mailData['to'][] = array('email' => $email, 'name' => $name);
    }

    public function setFrom($email, $name = '')
    {
        $this->_mailData['from'] = array('email' => $email, 'name' => $name);
    }
}
