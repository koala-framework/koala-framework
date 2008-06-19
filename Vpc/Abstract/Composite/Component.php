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
        $componentId = $this->getData()->componentId;
        foreach ($this->_getSetting('childComponentClasses') as $id=>$c) {
            $return[$id] = $componentId . '-' . $id;
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
}
