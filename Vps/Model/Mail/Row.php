<?php
class Vps_Model_Mail_Row extends Vps_Model_Proxy_Row
{
    protected $_mail;
    private $_mailData = array();
    protected $_additionalMailVarsRow = null;

    const GET_MAIL_CONTENT_AUTO = 'auto'; // html if possible, otherwise text
    const GET_MAIL_CONTENT_HTML = 'html';
    const GET_MAIL_CONTENT_TEXT = 'text';

    public function getMailContent($type = self::GET_MAIL_CONTENT_AUTO)
    {
        if ($this->mail_sent) {
            if ($type == self::GET_MAIL_CONTENT_AUTO) {
                if ($this->sent_mail_content_html) return $this->sent_mail_content_html;
                return $this->sent_mail_content_text;
            } else if ($type == self::GET_MAIL_CONTENT_HTML) {
                return $this->sent_mail_content_html;
            } else if ($type == self::GET_MAIL_CONTENT_TEXT) {
                return $this->sent_mail_content_text;
            } else {
                throw new Vps_Exception_NotYetImplemented();
            }
        } else {
            $mail = $this->_prepareMail();
            if ($mail instanceof Vps_Mail_Template) {
                return $mail->getMailContent($type);
            } else {
                throw new Vps_Exception_NotYetImplemented();
            }
        }
    }

    public function getAdditionalMailVarsRow()
    {
        return $this->_additionalMailVarsRow;
    }

    private function _prepareMail()
    {
        $siblingRows = $this->_getSiblingRows();
        $essentialsRow = $siblingRows['essentials'];
        $varsRow = $siblingRows['vars'];

        if (!empty($essentialsRow->masterTemplate)) {
            $mail = new $essentialsRow->mailerClass($essentialsRow->template, $essentialsRow->masterTemplate);
        } else {
            $mail = new $essentialsRow->mailerClass($essentialsRow->template);
        }

        $mail->vars = $varsRow;
        foreach ($varsRow->toArray() as $k => $v) {
            $mail->$k = $v;
        }

        $cc = $this->getCc();
        if ($cc) {
            foreach ($cc as $v) {
                $mail->addCc($v['email'], $v['name']);
            }
        }
        $header = $this->getHeader();
        if ($header) {
            foreach ($header as $v) {
                $mail->addHeader($v['name'], $v['value'], $v['append']);
            }
        }
        $bcc = $this->getBcc();
        if ($bcc) {
            foreach ($bcc as $v) {
                $mail->addBcc($v);
            }
        }
        $to = $this->getTo();
        if ($to) {
            foreach ($to as $v) {
                $mail->addTo($v['email'], $v['name']);
            }
        }
        $returnPath = $this->getReturnPath();
        if ($returnPath) {
            $mail->setReturnPath($returnPath);
        }
        $from = $this->getFrom();
        if ($from && $from['email']) {
            $mail->setFrom($from['email'], $from['name']);
        }
        $attachments = $this->getAttachments();
        if ($attachments) {
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment);
            }
        }

        // image helper bilder zu attachments hängen


/** TODO: das funzt noch nicht weil die image helper attachments im
Vps_Mail_Template direkt vor dem senden hinzugefügt werden. wie soll man da rankommen???
**/
d($mail->getView()->getImages());
foreach ($mail->getView()->getImages() as $image) {
    $this->_mail->addAttachment($image);
}

        return $mail;
    }

    public function sendMail()
    {
        if (!$this->mail_sent) {
            $mail = $this->_prepareMail();
            $mail->send();

            // save sent mail in database
            $this->sent_mail_content_text = $mail->getMailContent(self::GET_MAIL_CONTENT_TEXT);
            $this->sent_mail_content_html = $mail->getMailContent(self::GET_MAIL_CONTENT_HTML);
            $this->save();
        } else {
            $mail = new Vps_Mail();

            $items = $this->getTo();
            if ($items) { foreach ($items as $item) $mail->addTo($item['email'], $item['name']); }

            $item = $this->getFrom();
            if ($item) $mail->setFrom($item['email'], $item['name']);

            $item = $this->getReturnPath();
            if ($item) $mail->setReturnPath($item);

            $items = $this->getBcc();
            if ($items) { foreach ($items as $item) $mail->addBcc($item); }

            $items = $this->getCc();
            if ($items) { foreach ($items as $item) $mail->addCc($item['email'], $item['name']); }

            $items = $this->getAttachments();
            if ($items) { foreach ($items as $item) $mail->addAttachment($item); }

            $mail->setSubject($this->subject);
            $mail->setBodyText($this->sent_mail_content_text);
            if ($this->sent_mail_content_html) $mail->setBodyHtml($this->sent_mail_content_html);

            $mail->send();
        }
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
            'http_host' => (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''),
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

    public function addAttachment($file, $mailFilename = null)
    {
        $attachRow = $this->createChildRow('Attachments');
        if (is_object($file) && is_instance_of($file, 'Vps_Uploads_Row')) {
            // sollten uploads mal gelöscht werden, müssen diese auch in den
            // unterordner kopiert werden
            $attachRow->is_upload = 1;
            $attachRow->upload_model = get_class($file->getModel());
            $attachRow->filename = $file->id;
            $attachRow->mail_filename = (!is_null($mailFilename) ? $mailFilename : $file->filename.'.'.$file->extension);
            $attachRow->mime_type = $file->mime_type;
        } else {
            // die datei in einen uploads unterordner kopieren, könnte ja
            // zwischendurch mal geloescht werden
            $copyDir = $this->getModel()->getAttachmentSaveFolder();

            $fileMd5 = md5_file($file);
            $newFilepath = $copyDir.'/'.$fileMd5;
            if (!file_exists($newFilepath)) {
                copy($file, $newFilepath);
            }

            $attachRow->is_upload = 0;
            $attachRow->filename = $fileMd5;
            $attachRow->mail_filename = (!is_null($mailFilename) ? $mailFilename : basename($file));
            $attachRow->mime_type = Vps_Uploads_Row::detectMimeType(false, file_get_contents($newFilepath));
        }

        $attachRow->save();
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

    public function getCc()
    {
        $row = $this->_getEssentialsRow();
        if (!$row->cc) return array();
        return unserialize($row->cc);
    }

    public function getHeader()
    {
        $row = $this->_getEssentialsRow();
        if (!$row->header) return array();
        return unserialize($row->header);
    }

    public function getBcc()
    {
        $row = $this->_getEssentialsRow();
        if (!$row->bcc) return array();
        return unserialize($row->bcc);
    }

    public function getTo()
    {
        $row = $this->_getEssentialsRow();
        if (!$row->to) return array();
        return unserialize($row->to);
    }

    public function getReturnPath()
    {
        $row = $this->_getEssentialsRow();
        return $row->returnPath;
    }

    public function getFrom()
    {
        $row = $this->_getEssentialsRow();
        if (!$row->from) return array();
        return unserialize($row->from);
    }

    /**
     * @return An array of Zend_Mime_Part elements
     */
    public function getAttachments()
    {
        $ret = array();
        $attachmentRows = $this->getChildRows('Attachments');
        foreach ($attachmentRows as $attachmentRow) {
            if ($attachmentRow->is_upload) {
                $uploadRow = Vps_Model_Abstract::getInstance($attachmentRow->upload_model)
                    ->getRow($attachmentRow->filename);
                if (!$uploadRow) {
                    throw new Vps_Exception("UploadRow '".$attachmentRow->filename."' konnte in Upload Model '".$attachmentRow->upload_model."' nicht gefunden werden");
                }
                $mime = new Zend_Mime_Part(file_get_contents($uploadRow->getFileSource()));
                $mime->filename = $attachmentRow->mail_filename;
                $mime->encoding = Zend_Mime::ENCODING_BASE64;
                $mime->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                $mime->type = $attachmentRow->mime_type;
                $ret[] = $mime;
            } else {
                $copyDir = $this->getModel()->getAttachmentSaveFolder();

                $mime = new Zend_Mime_Part(file_get_contents($copyDir.'/'.$attachmentRow->filename));
                $mime->filename = $attachmentRow->mail_filename;
                $mime->encoding = Zend_Mime::ENCODING_BASE64;
                $mime->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                $mime->type = $attachmentRow->mime_type;
                $ret[] = $mime;
            }
        }

        return $ret;
    }
}
