<?php
class Vpc_Abstract_Composite_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'childComponentClasses' => array()
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsTabPanel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        foreach ($this->_getSetting('childComponentClasses') as $id=>$c) {
            $componentId = $this->getTreeCacheRow()->component_id.'-'.$id;
            $row = $this->getTreeCacheRow()->getTable()->find($componentId)->current();
            $return[$id] = $row->getComponent()->getTemplateVars();
        }
        return $return;
    }

    public function getSearchVars()
    {
        $ret = parent::getSearchVars();
        foreach ($this->_getSetting('childComponentClasses') as $id=>$c) {
            foreach ($this->getChildComponent($id)->getSearchVars() as $k=>$i) {
                if (!isset($ret[$k])) $ret[$k] = '';
                $ret[$k] .= ' '.$i;
            }
        }
        return $ret;
    }

    public function getStatisticVars()
    {
        $ret = parent::getStatisticVars();
        foreach ($this->_getSetting('childComponentClasses') as $id=>$c) {
            $ret = array_merge($ret, $this->getChildComponent($id)->getStatisticVars());
        }
        return $ret;
    }

    public function getChildComponent($type)
    {
        if (!isset($this->_childComponents[$type])) {
            $classes = $this->_getSetting('childComponentClasses');
            if (!isset($classes[$type])) {
                throw new Vps_Execption(trlVps("Invalid type: {0}, no such childComponent exists", $type));
            }
            $this->_childComponents[$type] = $this->createComponent($classes[$type], $type);
        }
        return $this->_childComponents[$type];
    }

    public function getChildComponents()
    {
        $ret = parent::getChildComponents();
        foreach ($this->_getSetting('childComponentClasses') as $id=>$c) {
            $ret[] = $this->getChildComponent($id);
        }
        return $ret;
    }
}
