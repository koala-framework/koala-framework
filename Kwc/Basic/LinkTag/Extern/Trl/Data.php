<?php
class Kwc_Basic_LinkTag_Extern_Trl_Data extends Kwf_Component_Data
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
    public function __get($var)
    {
        if ($var == 'url') {
            $row = $this->_getLinkRow();
            if (!$row) return $this->chained->url;
            return $row->target;
        } else if ($var == 'rel') {
            return $this->chained->rel;
        } else {
            return parent::__get($var);
        }
    }

    public function getAbsoluteUrl()
    {
        return $this->url;
    }
}
