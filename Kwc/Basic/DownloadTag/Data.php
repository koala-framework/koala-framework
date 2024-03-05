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
            $filename .= '.' . $fRow->extension;
            $ret = Kwf_Media::getUrl($this->componentClass, $this->componentId, 'default', $filename);
            $ev = new Kwf_Component_Event_CreateMediaUrl($this->componentClass, $this, $ret);
            Kwf_Events_Dispatcher::fireEvent($ev);
            return $ev->url;
        } else if ($var == 'rel') {
            $rel = array(parent::__get($var));
            $row = $this->_getLinkRow();
            if ($row && isset($row->rel_noindex) && $row->rel_noindex) {
                $rel[] = 'nofollow';
            }
            return implode(' ', array_unique($rel));
        } else {
            return parent::__get($var);
        }
    }

    private $_linkRow;
    private function _getLinkRow()
    {
        if (!isset($this->_linkRow)) {
            $m = Kwc_Abstract::createOwnModel($this->componentClass);
            $cols = array('open_type', 'rel_noindex');
            $this->_linkRow = (object)$m->fetchColumnsByPrimaryId($cols, $this->dbId);
        }
        return $this->_linkRow;
    }


    public function getAbsoluteUrl()
    {
        return $this->url;
    }

    public function getLinkDataAttributes()
    {
        $ret = parent::getLinkDataAttributes();
        $row = $this->_getLinkRow();
        if (!isset($row->open_type) || !$row->open_type) return '';
        if ($row->open_type == 'blank') {
            $ret['kwc-popup'] = 'blank';
        }
        return $ret;
    }

    public function getLinkClass()
    {
        return parent::getLinkClass().' kwfUp-kwcPopup';
    }
}
