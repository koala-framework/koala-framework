<?php
class Kwc_Basic_LinkTag_CommunityVideo_Data extends Kwf_Component_Data
{
    private $_linkData = false;
    private function _getLinkData()
    {
        if ($this->_linkData === false) {
            $this->_linkData = $this->getChildComponent('_video');
        }
        return $this->_linkData;
    }
    public function __get($var)
    {
        if ($var == 'url') {
            if (!$this->_getLinkData()->getComponent()->hasContent()) return '';
            return $this->_getLinkData()->url;
        } else if ($var == 'rel') {
            if (!$this->_getLinkData()->getComponent()->hasContent()) return '';
            return $this->_getLinkData()->rel;
        } else {
            return parent::__get($var);
        }
    }

    public function getLinkDataAttributes()
    {
        if (!$this->_getLinkData()->getComponent()->hasContent()) return array();
        return $this->chained->getLinkDataAttributes();
    }
}
