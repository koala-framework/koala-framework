<?php
class Kwc_Basic_LinkTag_Mail_Trl_Data extends Kwc_Basic_LinkTag_Abstract_Trl_Data
{
    private $_linkRow;
    private function _getLinkRow()
    {
        if (!isset($this->_linkRow)) {
            $m = Kwc_Abstract::createOwnModel($this->componentClass);
            $this->_linkRow = $m->getRow($this->dbId);
        }
        return $this->_linkRow;
    }
    
    public function getAbsoluteUrl()
    {
        $row = $this->_getLinkRow();
        if (!$row || !$row->mail) return '';
        return Kwc_Basic_LinkTag_Mail_Data::createHref($row, false);
    }

    public function __get($var)
    {
        if ($var == 'url') {
            $row = $this->_getLinkRow();
            if (!$row || !$row->mail) return '';
            return Kwc_Basic_LinkTag_Mail_Data::createHref($row);
        } else if ($var == 'url_mail_html') {
            $row = $this->_getLinkRow();
            if (!$row || !$row->mail) return '';
            return Kwc_Basic_LinkTag_Mail_Data::createHref($row, false);
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
