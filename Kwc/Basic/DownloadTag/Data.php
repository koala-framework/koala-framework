<?php
class Kwc_Basic_DownloadTag_Data extends Kwf_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            $m = Kwc_Abstract::createModel($this->componentClass);
            $row = $m->getRow($this->dbId);
            if (!$row) return null;
            $fRow = $row->getParentRow('File');
            if (!$fRow) return null;
            $filename = $row->filename;
            if (!$filename) {
                $filename = $fRow->filename;
            }
            $filename .= '.'.$fRow->extension;
            $ret = Kwf_Media::getUrl($this->componentClass, $this->componentId, 'default', $filename);
            $ev = new Kwf_Component_Event_CreateMediaUrl($this->componentClass, $this, $ret);
            Kwf_Events_Dispatcher::fireEvent($ev);
            return $ev->url;
        } else {
            return parent::__get($var);
        }
    }

    private $_linkRow;
    private function _getLinkRow()
    {
        if (!isset($this->_linkRow)) {
            $m = Kwc_Abstract::createOwnModel($this->componentClass);
            $cols = array('open_type', 'width', 'height', 'menubar', 'toolbar', 'locationbar', 'statusbar', 'scrollbars', 'resizable');
            $this->_linkRow = (object)$m->fetchColumnsByPrimaryId($cols, $this->dbId);
        }
        return $this->_linkRow;
    }


    public function getAbsoluteUrl()
    {
        return $this->url;
    }

    //this is the copy from Kwc_Basic_LinkTag_Extern_Data
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
        return parent::getLinkClass().' kwfUp-kwcPopup';
    }
}
