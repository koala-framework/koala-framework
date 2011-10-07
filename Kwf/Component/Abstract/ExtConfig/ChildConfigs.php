<?php
class Vps_Component_Abstract_ExtConfig_ChildConfigs extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig($childComponentKey = 'child')
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'child');
        $ret = array();
        foreach ($classes as $id=>$cls) {
            $c = Vps_Component_Abstract_ExtConfig_Abstract::getInstance($cls)->getConfig(Vps_Component_Abstract_ExtConfig_Abstract::TYPE_DEFAULT);
            foreach ($c as $k=>$i) {
                if (!isset($i['componentIdSuffix'])) $i['componentIdSuffix'] = '';
                $i['componentIdSuffix'] = '-'.$id . $i['componentIdSuffix'];
                $ret[$id.'-'.$k] = $i;
            }
        }
        return $ret;
    }
}