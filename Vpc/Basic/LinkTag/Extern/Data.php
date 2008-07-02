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
            $ret = '';
            $row = $this->_getLinkRow();
            if ($row->is_popup) {
                $pop = array();
                if ($row->width) $pop[] = 'width='.$row->width;
                if ($row->height) $pop[] = 'height='.$row->height;
                $pop[] = 'menubar='.($row->menubar ? 'yes' : 'no');
                $pop[] = 'toolbar='.($row->toolbar ? 'yes' : 'no');
                $pop[] = 'location='.($row->locationbar ? 'yes' : 'no');
                $pop[] = 'status='.($row->statusbar ? 'yes' : 'no');
                $pop[] = 'scrollbars='.($row->scrollbars ? 'yes' : 'no');
                $pop[] = 'resizable='.($row->resizable ? 'yes' : 'no');
                $ret = 'popup_'.implode(',', $pop);
            }
            return $ret;
        } else {
            return parent::__get($var);
        }
    }

}
