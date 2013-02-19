<?php
class Kwc_Abstract_Composite_ExtConfigTabs extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $classes = Kwc_Abstract::getChildComponentClasses($this->_class, 'child');

        $config = $this->_getStandardConfig('kwf.tabpanel', null);
        $config['activeTab'] = 0;
        $titles = array();
        foreach ($classes as $id=>$cls) {
            $c = array_values(Kwc_Admin::getInstance($cls)->getExtConfig());
            foreach ($c as $i) {
                //TODO: hier nicht den titel als index verwenden, das stinkt
                $componentIdSuffix = '-' . $id;
                if (isset($i['componentIdSuffix'])) {
                    $componentIdSuffix .= $i['componentIdSuffix'];
                }
                $i['componentIdSuffix'] = $componentIdSuffix;
                if (!isset($titles[$i['title']])) { $titles[$i['title']] = 0; }
                if ($titles[$i['title']]++ > 0) {
                    $i['title'] .= ' ' . $titles[$i['title']];
                }
                $config['tabs'][$i['title']] = $i;
            }
        }
        return array('tabs' => $config);
    }
}