<?php
class Kwc_Basic_LinkTag_Data extends Kwf_Component_Data
{
    private $_linkData = null;
    private function _getLinkData()
    {
        if ($this->_linkData === null) {
            $this->_linkData = $this->getChildComponent('-child');
            if (!$this->_linkData || is_instance_of($this->_linkData->componentClass, 'Kwc_Basic_None_Component')) {
                $this->_linkData = false;
            }
        }
        return $this->_linkData;
    }
    public function __get($var)
    {
        if ($var == 'url') {
            if (!$this->_getLinkData()) return '';
            return $this->_getLinkData()->url;
        } else if ($var == 'rel') {
            if (!$this->_getLinkData()) return '';
            return $this->_getLinkData()->rel;
        } else {
            return parent::__get($var);
        }
    }

    public function getAbsoluteUrl()
    {
        if (!$this->_getLinkData()) return '';
        return $this->_getLinkData()->getAbsoluteUrl();
    }

    public function getLinkDataAttributes()
    {
        if (!$this->_getLinkData()) return array();
        return $this->_getLinkData()->getLinkDataAttributes();
    }

    public function getLinkTitle()
    {
        if (!$this->_getLinkData()) return '';
        return $this->_getLinkData()->getLinkTitle();
    }
}
