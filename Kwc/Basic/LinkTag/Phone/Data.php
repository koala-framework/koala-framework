<?php
class Kwc_Basic_LinkTag_Phone_Data extends Kwc_Basic_LinkTag_Abstract_Data
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

    public static function createHref($row)
    {
        $ret = 'tel:';
        $ret .= $row->phone;
        return $ret;
    }

    public function __get($var)
    {
        if ($var == 'url') {
            $row = $this->_getLinkRow();
            if (!$row || !$row->phone) return '';
            return self::createHref($row);
        } else if ($var == 'rel') {
            return '';
        } else {
            return parent::__get($var);
        }
    }
}
