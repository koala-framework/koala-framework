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
