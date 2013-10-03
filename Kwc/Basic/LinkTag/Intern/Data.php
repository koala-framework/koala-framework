<?php
class Kwc_Basic_LinkTag_Intern_Data extends Kwf_Component_Data
{
    private $_data = array();
    private $_anchor = null;

    protected function _getData($select = array())
    {
        $m = Kwc_Abstract::createModel($this->componentClass);
        $result = $m->fetchColumnsByPrimaryId(array('target', 'anchor'), $this->dbId);
        if ($result) {
            $ret = null;
            $s = $select;
            $s['subroot'] = $this;
            $s['limit'] = 1;
            $components = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
                $result['target'],
                $s
            );
            if ($components) $ret = $components[0];
            if (!$ret) {
                $ret = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
                    $result['target'], $select
                );
            }
            if ($result['anchor']) {
                $this->_anchor = $result['anchor'];
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
            $ret = $this->getLinkedData()->url;
            if ($this->_anchor) {
                $ret .= '#' . $this->_anchor;
            }
            return $ret;
        } else if ($var == 'rel') {
            if (!$this->getLinkedData()) return '';
            return $this->getLinkedData()->rel;
        } else {
            return parent::__get($var);
        }
    }

}
