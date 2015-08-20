<?php
class Kwc_Basic_LinkTag_Data extends Kwf_Component_Data
{
    private $_linkData = false;
    private function _getLinkData()
    {
        if ($this->_linkData === false) {
            $this->_linkData = $this->getChildComponent('-child');
        }
        return $this->_linkData;
    }
    public function __get($var)
    {
        if ($var == 'url') {
            return $this->_getLinkData()->url;
        } else if ($var == 'rel') {
            return $this->_getLinkData()->rel;
        } else {
            return parent::__get($var);
        }
    }

    public function getAbsoluteUrl()
    {
        return $this->_getLinkData()->getAbsoluteUrl();
    }

    public function getLinkDataAttributes()
    {
        return $this->_getLinkData()->getLinkDataAttributes();
    }

    public function getLinkTitle()
    {
        return $this->_getLinkData()->getLinkTitle();
    }
}
