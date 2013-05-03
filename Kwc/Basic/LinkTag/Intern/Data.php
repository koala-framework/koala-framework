<?php
class Kwc_Basic_LinkTag_Intern_Data extends Kwf_Component_Data
{
    private $_data = array();

    protected function _getData($select = array())
    {
        $m = Kwc_Abstract::createModel($this->componentClass);
        $target = $m->fetchColumnByPrimaryId('target', $this->dbId);
        if ($target) {
            $ret = null;
            $s = $select;
            $s['subroot'] = $this;
            $s['limit'] = 1;
            $components = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
                $target,
                $s
            );
            if ($components) $ret = $components[0];
            if (!$ret) {
                $ret = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
                    $target, $select
                );
            }
            return $ret;
        }
        return false;
    }

    public final function getLinkedData($select = array())
    {
        $cacheId = serialize($select);
        if (!array_key_exists($cacheId, $this->_data)) {
            $this->_data[$cacheId] = $this->_getData($select);
        }
        return $this->_data[$cacheId];
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
