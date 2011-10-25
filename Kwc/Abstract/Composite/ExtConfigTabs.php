<?php
class Kwc_Abstract_Composite_ExtConfigTabs extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $classes = Kwc_Abstract::getChildComponentClasses($this->_class, 'child');

        $config = $this->_getStandardConfig('kwf.tabpanel', null);
        $config['activeTab'] = 0;
        foreach ($classes as $id=>$cls) {
            $c = array_values(Kwc_Admin::getInstance($cls)->getExtConfig());
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