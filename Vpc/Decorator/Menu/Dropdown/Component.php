<?php
/**
 * @package Vpc
 * @subpackage Decorator
 */
class Vpc_Decorator_Menu_Dropdown_Component extends Vpc_Decorator_Menu_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'pagetypes' => array('main')
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $pc = $this->getPageCollection();

        foreach ($this->_getSetting('pagetypes') as $type) {
            foreach ($return['menu'][$type] as $k=>$c) {
                $pages = $pc->getChildPages($c['page']);
                $return['menu'][$type][$k]['dropDown'] = $this->_getMenuData($pages);
            }
        }
        return $return;
    }
}
