<?php
class Kwc_Composite_SwitchDisplay_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('kwf.tabpanel', null);
        $config['activeTab'] = 0;

        //Link Text
        $config['tabs']['Link'] = $this->_getStandardConfig('kwf.autoform');
        $config['tabs']['Link']['title'] = trlKwf('Link');
        $config['tabs']['Link']['componentIdSuffix'] = '';

        $classes = Kwc_Abstract::getChildComponentClasses($this->_class, 'child');
        $cls = $classes['content'];
        $c = array_values(Kwc_Admin::getInstance($cls)->getExtConfig());
        foreach ($c as $i) {
            //TODO: hier nicht den titel als index verwenden, das stinkt
            $componentIdSuffix = '-content';
            if (isset($i['componentIdSuffix'])) {
                $componentIdSuffix .= $i['componentIdSuffix'];
            }
            $config['tabs'][$i['title']] = $i;
            $config['tabs'][$i['title']]['componentIdSuffix'] = $componentIdSuffix;
        }

        return array('tabs' => $config);
    }
}
