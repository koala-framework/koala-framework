<?php
class Vpc_Basic_LinkTag_Intern_Trl_Data extends Vps_Component_Data
{
    private $_data;
    private $_linkRow;

    private function _getLinkRow()
    {
        if (!isset($this->_linkRow)) {
            $m = Vpc_Abstract::createModel($this->componentClass);
            $this->_linkRow = $m->getRow($this->dbId);
        }
        return $this->_linkRow;
    }

    public final function getLinkedData()
    {
        if (!isset($this->_data)) {
            $masterLinkData = $this->chained->getLinkedData();
            if (!$masterLinkData) $this->_data = false;

            if ($masterLinkData) {
                $linkComponent = Vpc_Chained_Trl_Component::getChainedByMaster($masterLinkData, $this);
                if (!$linkComponent) {
                    $this->_data = false;
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
