<?php
class Vpc_Basic_LinkTag_Extern_Data extends Vps_Component_Data
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
            if (!$row) return '';
            return $row->target;
        } else if ($var == 'rel') {
            if (!Vpc_Abstract::getSetting($this->componentClass, 'hasPopup')) {
                $type = Vpc_Abstract::getSetting($this->componentClass, 'openType');
                if ($type == 'blank') {
                    return 'popup_blank';
                } else {
                    throw new Vps_Exception_NotYetImplemented();
                }
            }
            $ret = '';
            $row = $this->_getLinkRow();
            if (!$row) return '';
            if ($row->open_type == 'popup') {
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
            } else if ($row->open_type == 'blank') {
                $ret = 'popup_blank';
            }
            return $ret;
        } else {
            return parent::__get($var);
        }
    }
}
