<?php
abstract class Vpc_Basic_LinkTag_Intern_Trl_DataAbstract extends Vps_Component_Data
{
    private $_data;
    protected $_type = null; // 'cc' oder 'trl'

    public final function getLinkedData()
    {
        if (!isset($this->_data)) {
            $masterLinkData = $this->chained->getLinkedData();
            if (!$masterLinkData) $this->_data = false;

            if ($masterLinkData) {
                if (is_null($this->_type)) throw new Vps_Exception("_type may not be null");

                if ($this->_type == 'Trl') {
                    $linkComponent = Vpc_Chained_Trl_Component::getChainedByMaster($masterLinkData, $this);
                } else if ($this->_type == 'Cc') {
                    $linkComponent = Vpc_Chained_Cc_Component::getChainedByMaster($masterLinkData, $this);
                }
                if (!$linkComponent) {
                    $this->_data = false; //kann offline sein
                } else {
                    $this->_data = $linkComponent;
                }
            }
        }
        return $this->_data;
    }

    public function __get($var)
    {
        if ($var == 'url') {
            if (!$this->getLinkedData()) return '';
            return $this->getLinkedData()->url;
        } else if ($var == 'rel') {
            if (!$this->getLinkedData()) return '';
            return $this->getLinkedData()->rel;
        } else {
            return parent::__get($var);
        }
    }
}
