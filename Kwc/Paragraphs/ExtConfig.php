<?php
class Kwc_Paragraphs_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    private function _componentNameToArray($name, $component, &$componentList)
    {
        $names = explode('.', $name, 2);
        if (count($names) > 1) {
            $name = $names[0] . '>>'; // to have a unique key if a component with same name also exists
            $this->_componentNameToArray($names[1], $component, $componentList[$name]);
        } else {
            $componentList[$name] = $component;
        }
    }

    private static function _sortByPriority($a, $b)
    {
        $prioA = 0;
        if (Kwc_Abstract::hasSetting($a, 'componentPriority')) {
            $prioA = Kwc_Abstract::getSetting($a, 'componentPriority');
        }
        $prioB = 0;
        if (Kwc_Abstract::hasSetting($b, 'componentPriority')) {
            $prioB = Kwc_Abstract::getSetting($b, 'componentPriority');
        }
        return ($prioA < $prioB) ? 1 : -1;
    }

    protected function _getConfig()
    {
        $componentList = array();
        $componentIcons = array();
        $supportedMasterLayoutContexts = array();
        $categories = Kwc_Abstract::getSetting($this->_class, 'categories');
        foreach ($categories as $k=>$i) {
            $categories[$k] = Kwf_Registry::get('trl')->trlStaticExecute($i);
            $componentList[$categories[$k].'>>'] = array();
        }
        $components = $this->_getComponents();
        uasort($components, array('Kwc_Paragraphs_ExtConfig', '_sortByPriority'));
        foreach ($components as $component) {
            if (!Kwc_Abstract::hasSetting($component, 'componentName')) continue;
            $name = Kwc_Abstract::getSetting($component, 'componentName');
            $name = Kwf_Registry::get('trl')->trlStaticExecute($name);
            $icon = Kwc_Abstract::getSetting($component, 'componentIcon');
            if ($icon) {
                $icon = new Kwf_Asset($icon);
                $icon = $icon->__toString();
            }
            if ($name) {
                $cat = null;
                if (Kwc_Abstract::hasSetting($component, 'componentCategory')) {
                    $cat = Kwc_Abstract::getSetting($component, 'componentCategory');
                    if (isset($categories[$cat])) {
                        $cat = $categories[$cat];
                    }
                }
                if (!$cat) $cat = 'none';
                if (substr($name, 0, strlen($cat)+1) != $cat.'.') {
                    $name = $cat.'.'.$name;
                }
                $this->_componentNameToArray($name, $component, $componentList);
                $componentIcons[$component] = $icon;
                $supportedMasterLayoutContexts[$component] = Kwf_Component_Layout_Abstract::getInstance($component)->getSupportedContexts();
            }
        }

        //move content and none to top level
        $componentList = array_merge(
            $componentList['content>>'],
            $componentList['none>>'],
            $componentList
        );
        unset($componentList['content>>']);
        unset($componentList['none>>']);

        //remove empty categories
        foreach ($componentList as $k=>$i) {
            if (!$i) unset($componentList[$k]);
        }

        $config = $this->_getStandardConfig('kwc.paragraphs');
        $config['showDeviceVisible'] = Kwc_Abstract::getSetting($this->_class, 'useMobileBreakpoints');
        $config['components'] = $componentList;
        $config['componentIcons'] = $componentIcons;
        $config['supportedMasterLayoutContexts'] = $supportedMasterLayoutContexts;
        $config['needsComponentPanel'] = true;

        $config['generatorProperties'] = array();
        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_GeneratorProperty') as $plugin) {
            $prop = $plugin->getGeneratorProperty(Kwf_Component_Generator_Abstract::getInstance($this->_class, 'paragraphs'));
            if ($prop) {
                $config['generatorProperties'][] = $prop;
            }
        }

        return array(
            'paragraphs' => $config
        );
    }

    protected function _getComponents()
    {
        return Kwc_Abstract::getChildComponentClasses($this->_class, 'paragraphs');
    }
}
