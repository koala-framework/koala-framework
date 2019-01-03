<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_Mail_Row extends Kwf_Model_Proxy_Row
{
    protected $_mail;
    private $_mailData = array();
    private $_checkSpam = true;
    protected $_additionalMailVarsRow = null;

    const MAIL_CONTENT_AUTO = 'auto'; // html if possible, otherwise text
    const MAIL_CONTENT_HTML = 'html';
    const MAIL_CONTENT_TEXT = 'text';

    public function getMailContent($type = self::MAIL_CONTENT_AUTO)
    {
        if ($type == self::MAIL_CONTENT_AUTO) {
            if ($this->sent_mail_content_html) return $this->sent_mail_content_html;
            return $this->sent_mail_content_text;
        } else if ($type == self::MAIL_CONTENT_HTML) {
            return $this->sent_mail_content_html;
        } else if ($type == self::MAIL_CONTENT_TEXT) {
            return $this->sent_mail_content_text;
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
    }

    public function getAdditionalMailVarsRow()
    {
        return $this->_additionalMailVarsRow;
    }

    public function sendMail($transport = null)
    {
        if ($this->mail_sent) {
            throw new Kwf_Exception("'sendMail' may only be called once");
        }

        if (!$this->sent_mail_content_text) {
            throw new Kwf_Exception("text content must be set when sending a mail");
        }

        if ($this->is_spam) {
            $this->save();
            return;
        }

        $siblingRows = $this->_getSiblingRows();
        $essentialsRow = $siblingRows['essentials'];
        $varsRow = $siblingRows['vars'];

        $mail = new $essentialsRow->mailerClass();
        $mail->setBodyText($this->sent_mail_content_text);
        if ($this->sent_mail_content_html) $mail->setBodyHtml($this->sent_mail_content_html);
        $mail->setSubject($this->getSubject());

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
        $replyto = $this->getReplyTo();
        if ($replyto && $replyto['email']) {
            $mail->setReplyTo($replyto['email'], $replyto['name']);
        }
        $returnPath = $this->getReturnPath();
        if ($returnPath) {
            $mail->setReturnPath($returnPath);
        }
        $from = $this->getFrom();
        if ($from && $from['email']) {
            $mail->setFrom($from['email'], $from['name']);
        }
        $attachments = $this->_getAttachments();
        if ($attachments) {
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment);
            }
        }
        $mail->send($transport);

        $this->mail_sent = 1;
        $this->save();
    }

    private function _buildContentAndSetToRow()
    {
        // hier gehts ausschließlich um den mail-inhalt (html, txt, attachments)
        // eine mail class darf hier nicht drin sein

        $siblingRows = $this->_getSiblingRows();
        $essentialsRow = $siblingRows['essentials'];
        $varsRow = $siblingRows['vars'];

        if (!empty($essentialsRow->masterTemplate)) {
            $view = new Kwf_Mail_Template_View($essentialsRow->template, $essentialsRow->masterTemplate);
        } else {
            $view = new Kwf_Mail_Template_View($essentialsRow->template);
        }

        $view->vars = $varsRow;
        foreach ($varsRow->toArray() as $k => $v) {
            $view->$k = $v;
        }

        $this->sent_mail_content_text = $view->renderText();
        $this->sent_mail_content_html = $view->renderHtml();
    }

    protected function _beforeInsert()
    {
        $this->mail_sent = 0;
    }
    protected function _afterInsert()
    {
        parent::_afterInsert();

        $this->_buildContentAndSetToRow();

        $this->is_spam = $this->_checkIsSpam();
        $this->sendMail();
    }

    protected function _checkIsSpam()
    {
        if (!$this->_checkSpam) return false;

        if (!$this->id) throw new Kwf_Exception("row wurde noch nie gespeichert, daher spam check nicht möglich da keine id vorhanden");

        $siblingRows = $this->_getSiblingRows();
        $essentialsRow = $siblingRows['essentials'];
        $mailVarsRow = $siblingRows['vars'];

        $spamFields = unserialize($essentialsRow->spamFields);
        if (!$spamFields) return 0;

        if (in_array('*', $spamFields)) {
            $spamFields = array_keys($mailVarsRow->toArray());
        }
        $text = array();
        foreach ($spamFields as $v) {
            $text[] = $mailVarsRow->$v;
        }
        $text = implode("\n", $text);

        return Kwf_Util_Check_Spam::checkIsSpam($text, $this);
    }

    // sets for mail essentails
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

    public function setCheckSpam($v)
    {
        $this->_checkSpam = (bool)$v;
    }

    public function setMailerClass($mailerClass)
    {
        $row = $this->_getEssentialsRow();
        $row->mailerClass = $mailerClass;
    }

    private function _saveAttachmentData($file, $mailFilename = null, $mimeType = null)
    {
        $attachRow = $this->createChildRow('Attachments');
        $attachRow->is_upload = 0;

        // die datei in einen uploads unterordner kopieren, könnte ja
        // zwischendurch mal geloescht werden
        $copyDir = $this->getModel()->getAttachmentSaveFolder();

        if ($file instanceof Zend_Mime_Part) {
            $fileContent = $file->getContent();
            if ($file->encoding == Zend_Mime::ENCODING_BASE64) {
                $fileContent = base64_decode($fileContent);
            } else {
                throw new Kwf_Exception_NotYetImplemented("File encoding type '".$file->encoding."' not supported yet");
            }
            $fileMd5 = md5($file->getContent());
            $newFilepath = $copyDir.'/'.$fileMd5;
            if (!file_exists($newFilepath)) {
                file_put_contents($newFilepath, $fileContent);
            }

            $attachRow->mail_filename = $file->filename;
            $attachRow->mime_type = $file->type;
            if ($file->id) $attachRow->cid = $file->id;
        } else {
            $fileMd5 = md5_file($file);
            $newFilepath = $copyDir.'/'.$fileMd5;
            if (!file_exists($newFilepath)) {
                copy($file, $newFilepath);
            }

            $attachRow->mail_filename = (!is_null($mailFilename) ? $mailFilename : basename($file));
            $attachRow->mime_type = (!is_null($mimeType) ? $mimeType : Kwf_Uploads_Row::detectMimeType(false, file_get_contents($newFilepath)));
        }

        $attachRow->filename = $fileMd5;
        $attachRow->save();
    }

    public function addAttachment($file, $mailFilename = null)
    {
        if (is_object($file) && is_instance_of($file, 'Kwf_Uploads_Row')) {
            // sollten uploads mal gelöscht werden, müssen diese auch in den
            // unterordner kopiert werden
            $attachRow = $this->createChildRow('Attachments');
            $attachRow->is_upload = 1;
            $attachRow->upload_model = get_class($file->getModel());
            $attachRow->filename = $file->id;
            $attachRow->mail_filename = (!is_null($mailFilename) ? $mailFilename : $file->filename.'.'.$file->extension);
            $attachRow->mime_type = $file->mime_type;
            $attachRow->save();
        } else {
            $this->_saveAttachmentData($file, $mailFilename);
        }
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

    public function setReplyTo($email, $name = '')
    {
        $row = $this->_getEssentialsRow();
        $row->replyto = serialize(array('email' => $email, 'name' => $name));
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

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        if (!empty($this->subject)) return $this->subject;
        return null;
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

    public function getReplyTo()
    {
        $row = $this->_getEssentialsRow();
        if (!$row->replyto) return array();
        return unserialize($row->replyto);
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
    private function _getAttachments()
    {
        $ret = array();
        $attachmentRows = $this->getChildRows('Attachments');
        foreach ($attachmentRows as $attachmentRow) {
            if ($attachmentRow->is_upload) {
                $uploadRow = Kwf_Model_Abstract::getInstance($attachmentRow->upload_model)
                    ->getRow($attachmentRow->filename);
                if (!$uploadRow) {
                    throw new Kwf_Exception("UploadRow '".$attachmentRow->filename."' konnte in Upload Model '".$attachmentRow->upload_model."' nicht gefunden werden");
                }
                $mime = new Zend_Mime_Part(file_get_contents($uploadRow->getFileSource()));
                Kwf_Util_Upload::onFileRead($uploadRow->getFileSource());
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
                if ($attachmentRow->cid) $mime->id = $attachmentRow->cid;
                $ret[] = $mime;
            }
        }

        return $ret;
    }
}
