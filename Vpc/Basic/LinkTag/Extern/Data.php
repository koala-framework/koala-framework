<?php
class Vpc_Basic_LinkTag_Extern_Data extends Vps_Component_Data
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
            return $this->_getLinkRow()->target;
        } else if ($var == 'rel') {
            $ret = array();
            $row = $this->_getLinkRow();
            if ($row->is_popup) {
                if ($row->width) $ret[] = 'width='.$row->width;
                if ($row->height) $ret[] = 'height='.$row->height;
                $ret[] = 'menubar='.($row->menubar ? 'yes' : 'no');
                $ret[] = 'toolbar='.($row->toolbar ? 'yes' : 'no');
                $ret[] = 'location='.($row->locationbar ? 'yes' : 'no');
                $ret[] = 'status='.($row->statusbar ? 'yes' : 'no');
                $ret[] = 'scrollbars='.($row->scrollbars ? 'yes' : 'no');
                $ret[] = 'resizable='.($row->resizable ? 'yes' : 'no');
            }
            return implode(',', $ret);
        } else {
            return parent::__get($var);
        }
    }

}
