<?php
/**
 * Menübox. Speichert in Template-Variable alle Werte, die
 * für das Menü benötigt werden.
 * @package Kwc
 */
class Kwc_Menu_Component extends Kwc_Menu_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['subMenu'] = array(
            'class' => 'Kwc_Menu_Generator',
            'component' => false
        );
        $ret['separator'] = '';
        $ret['linkPrefix'] = '';
        $ret['emptyIfSingleEntry'] = false;
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (isset($settings['maxLevel'])) {
            if (is_numeric($settings['level'])) {
                if ($settings['level'] < $settings['maxLevel']) {
                    throw new Kwf_Exception("maxLevel setting doesn't exist anymore, you need to manually create a submenu");
                }
            } else {
                if ($settings['maxLevel'] > 1) {
                    throw new Kwf_Exception("maxLevel setting doesn't exist anymore, you need to manually create a submenu");
                }
            }
            throw new Kwf_Exception("maxLevel setting doesn't exist anymore, please simply remove");
        }
    }

    protected static function _requiredLevels($componentClass)
    {
        $requiredLevels= parent::_requiredLevels($componentClass);

        $generators = Kwc_Abstract::getSetting($componentClass, 'generators');
        while (isset($generators['subMenu'])) {
            $class = $generators['subMenu']['component']['subMenu'];
            if (!is_instance_of($class, 'Kwc_Menu_Abstract_Component')) break;
            $generators = Kwc_Abstract::getSetting($class, 'generators');
            $requiredLevels++;
        }
        return $requiredLevels;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
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
        return false;
    }
}
