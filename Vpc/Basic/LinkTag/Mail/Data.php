<?php
class Vpc_Basic_LinkTag_Mail_Data extends Vps_Component_Data
{
    private $_linkRow;
    private function _getLinkRow()
    {
        if (!isset($this->_linkRow)) {
            $t = Vpc_Abstract::getSetting($this->componentClass, 'tablename');
            $table = new $t(array('componentClass' => $this->componentClass));
            $this->_linkRow = $table->find($this->dbId)->current();
        }
        return $this->_linkRow;
    }
    public function __get($var)
    {
        if ($var == 'url') {
            $row = $this->_getLinkRow();
            if (!$row) return '';
            $ret = 'mailto:';

            // helper wegen spamschutz
            $helper = new Vps_View_Helper_MailLink();
            $ret .= $helper->encodeMail($row->mail);

            if ($row->text || $row->subject) $ret .= '?';
            if ($row->subject) $ret .= 'subject='.$row->subject;
            if ($row->text && $row->subject) $ret .= '&';
            if ($row->text) $ret .= 'body='.$row->text;
            return $ret;
        } else if ($var == 'rel') {
            return '';
        } else {
            return parent::__get($var);
        }
    }

}
