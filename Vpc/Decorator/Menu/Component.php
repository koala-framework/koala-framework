<?php
/**
 * Menüdecorator. Speichert in Template-Variable alle Werte, die
 * für das Menü benötigt werden.
 * @package Vpc
 * @subpackage Decorator
 */
class Vpc_Decorator_Menu_Component extends Vpc_Decorator_Abstract
{
    protected $_levels = 2;
    
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $pc = $this->getPageCollection();

        // Array mit IDs von aktueller Seiten und Parent Pages
        $currentPageIds = array();
        $p = $pc->getCurrentPage();
        do {
            $currentPageIds[] = $p->getPageId();
        } while($p = $pc->getParentPage($p));

        // Hauptmenü
        $config = new Zend_Config_Ini('application/config.ini', 'pagecollection');
        foreach ($config->pagecollection->pagetypes as $type => $i) {
            $pages = $pc->getChildPages(null, $type);
            $return['menu'][$type] = array();
            $return['menu'][$type] = $this->_getMenuData($pages, $currentPageIds);
        }

        // Submenüs
        $level = 0;
        $page = $pc->findPage(array_pop($currentPageIds));
        while ($page && $level < $this->_levels) {
            $pages = $pc->getChildPages($page);
            $return['submenus'][$level] = $this->_getMenuData($pages, $currentPageIds);
            $page = $pc->findPage(array_pop($currentPageIds));
            $level++;
        }
        return $return;
    }
    
    protected function _getMenuData($pages, $currentPageIds)
    {
        $return = array();
        foreach ($pages as $i => $page) {
            $data = $this->getPageCollection()->getPageData($page);
            if ($data['hide']) { continue; }
            $class = '';
            if ($i == 0) $class .= ' first';
            if ($i == sizeof($pages)-1) $class .= ' last';
            $isCurrent = in_array($page->getPageId(), $currentPageIds);
            if ($isCurrent) $class .= ' current';
            $return[] = array(
                'href'    => $data['url'],
                'text'    => $data['name'],
                'current' => $isCurrent,
                'class'   => trim($class),
                'rel'     => $data['rel']
            );
        }
        return $return;
    }
}
