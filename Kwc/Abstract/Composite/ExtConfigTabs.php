<?php
class Vpc_Abstract_Composite_ExtConfigTabs extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'child');

        $config = $this->_getStandardConfig('vps.tabpanel', null);
        $config['activeTab'] = 0;
        foreach ($classes as $id=>$cls) {
            $c = array_values(Vpc_Admin::getInstance($cls)->getExtConfig());
            foreach ($c as $i) {
                //TODO: hier nicht den titel als index verwenden, das stinkt
                $componentIdSuffix = '-' . $id;
                if (isset($i['componentIdSuffix'])) {
                    $componentIdSuffix .= $i['componentIdSuffix'];
                }
                $config['tabs'][$i['title']] = $i;
                $config['tabs'][$i['title']]['componentIdSuffix'] = $componentIdSuffix;
            }
        }
        return array('tabs' => $config);
    }
}