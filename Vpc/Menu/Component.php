<?php
/**
 * Menübox. Speichert in Template-Variable alle Werte, die
 * für das Menü benötigt werden.
 * @package Vpc
 */
class Vpc_Menu_Component extends Vpc_Menu_Abstract
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
        $ret['showParentPage'] = false;
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['menu'] = $this->_getMenuData();
        $ret['level'] = $this->_getSetting('level');
        $ret['subMenu'] = $this->getData()->getChildComponent('-subMenu');
        $ret['separator'] = $this->_getSetting('separator');
        $ret['linkPrefix'] = $this->_getSetting('linkPrefix');
        $ret['parentPage'] = null;
        if ($this->_getSetting('showParentPage')) {
            $currentPages = array_reverse($this->_getCurrentPages());
            if (isset($this->getData()->level)) {
                $level = $this->getData()->level;
            } else {
                $level = $this->_getSetting('level');
            }
            if (is_string($level)) {
                throw new Vps_Exception("You can't use showParentMenu for MainMenus (what should that do?)");
            }
            if (isset($currentPages[$level-2])) {
                $ret['parentPage'] = $currentPages[$level-2];
            }
        }
        return $ret;
    }
    public function hasContent()
    {
        if (count($this->_getMenuData())) return true;
        $sub = $this->getData()->getChildComponent('-subMenu');
        if ($sub && $sub->getComponent()->hasContent()) {
            return true;
        }
        return false;
    }
}
