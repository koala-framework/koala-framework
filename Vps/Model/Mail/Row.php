<?php
class Vps_Model_Mail_Row extends Vps_Model_FnF_Row //Vps_Model_Db_Row
{
    protected $_mail;
    private $_mailData = array();
    protected $_additionalMailVarsRow = null;

    public function getAdditionalMailVarsRow()
    {
        return $this->_additionalMailVarsRow;
    }

    static public function sendMail($essentialsRow, $varsRow)
    {
        if (!empty($essentialsRow->masterTemplate)) {
            $mail = new Vps_Mail($essentialsRow->template, $essentialsRow->masterTemplate);
        } else {
            $mail = new Vps_Mail($essentialsRow->template);
        }

        foreach ($varsRow->toArray() as $k => $v) {
            $mail->$k = $v;
        }

        if (isset($essentialsRow->cc)) {
            $arr = $essentialsRow->cc;
            foreach ($arr as $v) {
                $mail->addCc($v['email'], $v['name']);
            }
        }
        if (isset($essentialsRow->header)) {
            $arr = $essentialsRow->header;
            foreach ($arr as $v) {
                $mail->addHeader($v['name'], $v['value'], $v['append']);
            }
        }
        if (isset($essentialsRow->bcc)) {
            $arr = $essentialsRow->bcc;
            foreach ($arr as $v) {
                $mail->addBcc($v['email']);
            }
        }
        if (isset($essentialsRow->to)) {
            $arr = $essentialsRow->to;
            foreach ($arr as $v) {
                $mail->addTo($v['email'], $v['name']);
            }
        }
        if (isset($essentialsRow->returnPath)) {
            $mail->setReturnPath($essentialsRow->returnPath['email']);
        }
        if (isset($essentialsRow->from)) {
            $mail->setFrom($essentialsRow->from['email'], $essentialsRow->from['name']);
        }

        $mail->send();
    }

    public function save()
    {
        $saveModel = $this->getModel()->getSaveModel();
        if ($saveModel) {
            $row = $saveModel->createRow();
            $row->is_spam = 0;
            $row->mail_sent = 0;
            $row->save();
        }

        $addMailVarsModel = $this->getModel()->getAdditionalMailVarsModel();
        if ($addMailVarsModel) {
            $this->_additionalMailVarsRow = $addMailVarsModel->createRow();
            foreach ($this->_data as $k => $v) {
                $this->_additionalMailVarsRow->$k = $v;
            }
            $this->_additionalMailVarsRow->save();
        }

        $mailVarsModel = $this->getModel()->getSaveMailVarsModel();
        if ($mailVarsModel) {
            if ($mailVarsModel instanceof Vps_Model_Field) {
                $mailVarsRow = $mailVarsModel->getRowByParentRow($row);
            } else {
                $mailVarsRow = $mailVarsModel->createRow();
            }

            foreach ($this->_data as $k => $v) {
                $mailVarsRow->$k = $v;
            }
            $mailVarsRow->save();
        }

        $mailEssentialsModel = $this->getModel()->getSaveMailEssentialsModel();
        if ($mailEssentialsModel) {
            if ($mailEssentialsModel instanceof Vps_Model_Field) {
                $mailEssentialsRow = $mailEssentialsModel->getRowByParentRow($row);
            } else {
                $mailEssentialsRow = $mailEssentialsModel->createRow();
            }

            foreach ($this->_mailData as $k => $v) {
                $mailEssentialsRow->$k = $v;
            }
            $tpl = $this->getModel()->getMailTemplate();
            if (!$tpl) {
                throw new Vps_Exception("Mail template not set for model '".get_class($this->getModel())."'");
            }
            $mailEssentialsRow->template = $tpl;

            $masterTpl = $this->getModel()->getMailMasterTemplate();
            if ($masterTpl) {
                $mailEssentialsRow->masterTemplate = $masterTpl;
            }

            $mailEssentialsRow->save();
        }

        $row->is_spam = $this->_checkIsSpam($mailVarsRow);
        $row->save();

        if (!$row->is_spam) {
            self::sendMail($mailEssentialsRow, $mailVarsRow);
            $row->mail_sent = 1;
            $row->save();
        }

        parent::save();
    }

    static public function getSpamKey($enquiriesRow)
    {
        return substr(md5(serialize($enquiriesRow->id.$enquiriesRow->save_date)), 0, 15);
    }

    private function _checkIsSpam($mailVarsRow)
    {
        $spamFields = $this->getModel()->getSpamFields();
        if (!$spamFields) return 0;

        if (in_array('*', $spamFields)) {
            $spamFields = array_keys($mailVarsRow->toArray());
        }

        $additionalData = array(
            'http_host' => $_SERVER['HTTP_HOST'],
            'ham_url' => '/vps/spam/set?id='.$mailVarsRow->getParentRow()->id.'&value=0&key='.self::getSpamKey($mailVarsRow->getParentRow())
        );

        require_once "HTTP/Request.php";

        $text = array();
        foreach ($spamFields as $v) {
            $text[] = $mailVarsRow->$v;
        }
        $text = implode("\n", $text);

        $req = new HTTP_Request('http://cms.vivid-planet.com/spamfilter/check.php?method=checkSpam');
        $req->setMethod(HTTP_REQUEST_METHOD_POST);
        $req->addPostData('text', (strlen($text) > 2000 ? substr($text, 0, 2000) : $text));
        $req->addPostData('additional_data', $additionalData);
        $res = $req->sendRequest();
        if ($res) {
            return $req->getResponseBody();
        }
        return 0;
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
