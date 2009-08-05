<?php
class Vpc_Basic_LinkTag_Mail_Data extends Vps_Component_Data
{
    private $_linkRow;
    private function _getLinkRow()
    {
        if (!isset($this->_linkRow)) {
            $m = Vpc_Abstract::createModel($this->componentClass);
            $this->_linkRow = $m->getRow($this->dbId);
        }
        return $this->_linkRow;
    }
    public function __get($var)
    {
        if ($var == 'url') {
            $row = $this->_getLinkRow();
            if (!$row || !$row->mail) return '';
            $ret = 'mailto:';

            // helper wegen spamschutz
            $helper = new Vps_View_Helper_MailLink();
            $ret .= $helper->encodeMail($row->mail);

            if ($row->text || $row->subject) $ret .= '?';
            if ($row->subject) $ret .= 'subject='.$row->subject;
            if ($row->text && $row->subject) $ret .= '&';
            if ($row->text) $ret .= 'body='.$row->text;
            return $ret;
        } else if ($var == 'url_mail_html') {
            $row = $this->_getLinkRow();
            if (!$row || !$row->mail) return '';
            $ret = 'mailto:';

            $ret .= $row->mail;
            if ($row->text || $row->subject) $ret .= '?';
            if ($row->subject) $ret .= 'subject='.$row->subject;
            if ($row->text && $row->subject) $ret .= '&';
            if ($row->text) $ret .= 'body='.$row->text;
            return $ret;
        } else if ($var == 'url_mail_txt') {
            $row = $this->_getLinkRow();
            return ((!$row || !$row->mail) ? '' : $row->mail);
        } else if ($var == 'rel') {
            return '';
        } else {
            return parent::__get($var);
        }
    }

}
