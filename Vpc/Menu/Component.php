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
        $ret['maxLevel'] = 1;
        $ret['generators']['subMenu'] = array(
            'class' => 'Vpc_Menu_Generator',
            'component' => 'Vpc_Menu_Component'
        );
        $ret['separator'] = '';
        $ret['linkPrefix'] = '';
        $ret['cssClass'] = 'webStandard printHidden';
        $ret['emptyIfSingleEntry'] = false;
        return $ret;
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
