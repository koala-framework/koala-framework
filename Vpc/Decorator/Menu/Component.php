<?php
/**
 * Menüdecorator. Speichert in Template-Variable alle Werte, die
 * für das Menü benötigt werden.
 * @package Vpc
 * @subpackage Decorator
 */
class Vpc_Decorator_Menu_Component extends Vpc_Decorator_Menu_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'levels' => 2
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $pc = $this->getPageCollection();


        // Hauptmenü
        $config = new Zend_Config_Ini('application/config.ini', 'pagecollection');
        foreach ($config->pagecollection->pagetypes as $type => $i) {
            $pages = $pc->getChildPages(null, $type);
            $return['menu'][$type] = $this->_getMenuData($pages);
        }

        // Submenüs
        $level = 0;
        $currentPageIds = $this->_getCurrentPageIds();
        $page = $pc->findPage(array_pop($currentPageIds));
        while ($page && $level < $this->_getSetting('levels')) {
            $pages = $pc->getChildPages($page);
            $return['submenus'][$level] = $this->_getMenuData($pages);
            $page = $pc->findPage(array_pop($currentPageIds));
            $level++;
        }
        return $return;
    }

}
