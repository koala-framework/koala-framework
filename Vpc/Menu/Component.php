<?php
/**
 * Menübox. Speichert in Template-Variable alle Werte, die
 * für das Menü benötigt werden.
 * @package Vpc
 */
class Vpc_Menu_Component extends Vpc_Menu_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['subMenu'] = array(
            'class' => 'Vpc_Menu_Generator',
            'component' => false
        );
        $ret['separator'] = '';
        $ret['linkPrefix'] = '';
        $ret['cssClass'] = 'webStandard printHidden';
        $ret['emptyIfSingleEntry'] = false;
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (isset($settings['maxLevel'])) {
            if (is_numeric($settings['level'])) {
                if ($settings['level'] < $settings['maxLevel']) {
                    throw new Vps_Exception("maxLevel setting doesn't exist anymore, you need to manually create a submenu");
                }
            } else {
                if ($settings['maxLevel'] > 1) {
                    throw new Vps_Exception("maxLevel setting doesn't exist anymore, you need to manually create a submenu");
                }
            }
            throw new Vps_Exception("maxLevel setting doesn't exist anymore, please simply remove");
        }
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        $menuLevel = self::_getMenuLevel($componentClass, $parentData, $generator);
        $generators = Vpc_Abstract::getSetting($componentClass, 'generators');
        $level = Vpc_Abstract::getSetting($componentClass, 'level');
        if (!is_numeric($level)) $level = 1;
        while (isset($generators['subMenu'])) {
            $class = $generators['subMenu']['component'];
            if (!is_instance_of($class, 'Vpc_Menu_Abstract_Component')) break;
            $generators = Vpc_Abstract::getSetting($class, 'generators');
            $level++;
        }
        return $menuLevel > $level;
    }

    public function getMenuComponent()
    {
        $menuComponent = $this->getData();
        $component = $menuComponent->parent;
        while ($component && $menuComponent &&
            !Vpc_Abstract::getFlag($component->componentClass, 'menuCategory') &&
            $component->componentId != $this->_getSetting('level')
        ) {
            $menuComponent = $menuComponent->getChildComponent('-subMenu');
            $component = $component->parent;
        }
        $ret = null;
        if ($this->getPageComponent() && $component->componentId == $this->getPageComponent()->componentId &&
            $menuComponent && $menuComponent->getComponent()->_getMenuData()
        ) {
            $ret = $menuComponent;
        }
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['menu'] = $this->_getMenuData();
        if ($this->_getSetting('emptyIfSingleEntry')) {
            if (count($ret['menu']) == 1) {
                $ret['menu'] = array();
            }
        }
        $ret['subMenu'] = $this->getData()->getChildComponent('-subMenu');
        $ret['separator'] = $this->_getSetting('separator');
        $ret['linkPrefix'] = $this->_getSetting('linkPrefix');
        return $ret;
    }

    public function hasContent()
    {
        $c = count($this->_getMenuData());
        if ($this->_getSetting('emptyIfSingleEntry')) {
            if ($c > 1) return true;
        } else if ($c > 0) {
            return true;
        }
        $sub = $this->getData()->getChildComponent('-subMenu');
        if ($sub && $sub->getComponent()->hasContent()) {
            return true;
        }
        return false;
    }
}
