<?php
class Kwc_Basic_LinkTag_Extern_Data extends Kwc_Basic_LinkTag_Abstract_Data
{
    private $_linkRow;
    private function _getLinkRow()
    {
        if (!isset($this->_linkRow)) {
            $m = Kwc_Abstract::createOwnModel($this->componentClass);
            $cols = array('target', 'open_type', 'width', 'height', 'menubar', 'toolbar', 'locationbar', 'statusbar', 'scrollbars', 'resizable', 'rel_noindex');
            $this->_linkRow = (object)$m->fetchColumnsByPrimaryId($cols, $this->dbId);
        }
        return $this->_linkRow;
    }

    public function __get($var)
    {
        if ($var == 'url') {
            $row = $this->_getLinkRow();
            if (!isset($row->target) || !$row->target) return '';
            return $row->target;
        } else if ($var == 'rel') {
            $rel = array(parent::__get($var));
            $row = $this->_getLinkRow();
            if ($row->rel_noindex) {
                $rel[] = 'nofollow';
            }
            return implode(' ', array_unique($rel));
        } else {
            return parent::__get($var);
        }
    }

    public function getAbsoluteUrl()
    {
        return $this->url;
    }

    public function getLinkDataAttributes()
    {
        $ret = parent::getLinkDataAttributes();
        if (!Kwc_Abstract::getSetting($this->componentClass, 'hasPopup')) {
            $type = Kwc_Abstract::getSetting($this->componentClass, 'openType');
            if ($type == 'blank') {
                $ret['kwc-popup'] = 'blank';
            } else {
                throw new Kwf_Exception_NotYetImplemented();
            }
        }
        $row = $this->_getLinkRow();
        if (!isset($row->open_type) || !$row->open_type) return '';
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
            $ret['kwc-popup'] = implode(',', $pop);
        } else if ($row->open_type == 'blank') {
            $ret['kwc-popup'] = 'blank';
        }
        return $ret;
    }

    public function getLinkClass()
    {
        return 'kwfUp-kwcPopup';
    }
}
