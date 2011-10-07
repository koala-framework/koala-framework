<?php
class Kwf_Component_Abstract_ExtConfig_ChildConfigs extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig($childComponentKey = 'child')
    {
        $classes = Kwc_Abstract::getChildComponentClasses($this->_class, 'child');
        $ret = array();
        foreach ($classes as $id=>$cls) {
            $c = Kwf_Component_Abstract_ExtConfig_Abstract::getInstance($cls)->getConfig(Kwf_Component_Abstract_ExtConfig_Abstract::TYPE_DEFAULT);
            foreach ($c as $k=>$i) {
                if (!isset($i['componentIdSuffix'])) $i['componentIdSuffix'] = '';
                $i['componentIdSuffix'] = '-'.$id . $i['componentIdSuffix'];
                $ret[$id.'-'.$k] = $i;
            }
        }
        return $ret;
    }
}