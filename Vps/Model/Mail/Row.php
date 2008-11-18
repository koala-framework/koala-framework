<?php
class Vps_Model_Mail_Row extends Vps_Model_Proxy_Row
{
    protected $_mail;
    private $_mailData = array();
    protected $_additionalMailVarsRow = null;

    public function getAdditionalMailVarsRow()
    {
        return $this->_additionalMailVarsRow;
    }

    public function sendMail()
    {
        $siblingRows = $this->_getSiblingRows();
        $essentialsRow = $siblingRows['essentials'];
        $varsRow = $siblingRows['vars'];

        if (!empty($essentialsRow->masterTemplate)) {
            $mail = new $essentialsRow->mailerClass($essentialsRow->template, $essentialsRow->masterTemplate);
        } else {
            $mail = new $essentialsRow->mailerClass($essentialsRow->template);
        }

        foreach ($varsRow->toArray() as $k => $v) {
            $mail->$k = $v;
        }

        if ($essentialsRow->cc) {
            $arr = unserialize($essentialsRow->cc);
            foreach ($arr as $v) {
                $mail->addCc($v['email'], $v['name']);
            }
        }
        if ($essentialsRow->header) {
            $arr = unserialize($essentialsRow->header);
            foreach ($arr as $v) {
                $mail->addHeader($v['name'], $v['value'], $v['append']);
            }
        }
        if ($essentialsRow->bcc) {
            $arr = unserialize($essentialsRow->bcc);
            foreach ($arr as $v) {
                $mail->addBcc($v);
            }
        }
        if ($essentialsRow->to) {
            $arr = unserialize($essentialsRow->to);
            foreach ($arr as $v) {
                $mail->addTo($v['email'], $v['name']);
            }
        }
        if ($essentialsRow->returnPath) {
            $mail->setReturnPath($essentialsRow->returnPath);
        }
        if ($essentialsRow->from) {
            $from = unserialize($essentialsRow->from);
            $mail->setFrom($from['email'], $from['name']);
        }

        $mail->send();
    }


    protected function _beforeInsert()
    {
        $this->mail_sent = 0;
    }

    protected function _afterInsert()
    {
        // checkIsSpam brauch eine ID, deshalb im afterInsert
        $this->is_spam = $this->_checkIsSpam();
        if (!$this->is_spam) {
            $this->sendMail();
            $this->mail_sent = 1;
        }
        $this->save();
    }

    static public function getSpamKey($enquiriesRow)
    {
        return substr(md5(serialize($enquiriesRow->id.$enquiriesRow->save_date)), 0, 15);
    }

    private function _checkIsSpam()
    {
        $siblingRows = $this->_getSiblingRows();
        $essentialsRow = $siblingRows['essentials'];
        $mailVarsRow = $siblingRows['vars'];

        $spamFields = unserialize($essentialsRow->spamFields);
        if (!$spamFields) return 0;

        if (in_array('*', $spamFields)) {
            $spamFields = array_keys($mailVarsRow->toArray());
        }

        $additionalData = array(
            'http_host' => $_SERVER['HTTP_HOST'],
            'ham_url' => '/vps/spam/set?id='.$this->id.'&value=0&key='.self::getSpamKey($this)
        );

        $text = array();
        foreach ($spamFields as $v) {
            $text[] = $mailVarsRow->$v;
        }
        $text = implode("\n", $text);

        $req = new Zend_Http_Client('http://cms.vivid-planet.com/spamfilter/check.php?method=checkSpam');
        $req->setMethod(Zend_Http_Client::POST);
        $req->setParameterPost('text', (strlen($text) > 2000 ? substr($text, 0, 2000) : $text));
        $req->setParameterPost('additional_data', $additionalData);
        $res = $req->request();
        if ($res) {
            return $res->getBody();
        }
        return 0;
    }

    // sets für mail essentails
    private function _getEssentialsRow()
    {
        $siblingRows = $this->_getSiblingRows();
        return $siblingRows['essentials'];
    }

    private function _addToSerializedEssentialsColumn($column, $data)
    {
        $row = $this->_getEssentialsRow();
        $ret = array();
        if (!empty($row->$column)) {
            $ret = unserialize($row->$column);
        }
        $ret[] = $data;
        $row->$column = serialize($ret);
    }

    public function setTemplate($tpl)
    {
        $row = $this->_getEssentialsRow();
        $row->template = $tpl;
    }

    public function setMasterTemplate($tpl)
    {
        $row = $this->_getEssentialsRow();
        $row->masterTemplate = $tpl;
    }

    public function setSpamFields(array $spamFields = array())
    {
        $row = $this->_getEssentialsRow();
        $row->spamFields = serialize($spamFields);
    }

    public function setMailerClass($mailerClass)
    {
        $row = $this->_getEssentialsRow();
        $row->mailerClass = $mailerClass;
    }


    public function addCc($email, $name = '')
    {
        $this->_addToSerializedEssentialsColumn(
            'cc', array('email' => $email, 'name' => $name)
        );
    }

    public function addHeader($name, $value, $append = false)
    {
        $this->_addToSerializedEssentialsColumn(
            'header', array('name' => $name, 'value' => $value, 'append' => $append)
        );
    }

    public function addBcc($email)
    {
        $this->_addToSerializedEssentialsColumn('bcc', $email);
    }

    public function addTo($email, $name = '')
    {
        $this->_addToSerializedEssentialsColumn(
            'to', array('email' => $email, 'name' => $name)
        );
    }

    public function setReturnPath($email)
    {
        $row = $this->_getEssentialsRow();
        $row->returnPath = $email;
    }

    public function setFrom($email, $name = '')
    {
        $row = $this->_getEssentialsRow();
        $row->from = serialize(array('email' => $email, 'name' => $name));
    }
}
