<?php
/**
 * Menüdecorator. Speichert in Template-Variable alle Werte, die 
 * für das Menü benötigt werden.
 * @package Vpc
 * @subpackage Decorator
 */
class Vpc_Decorator_Menu_Component extends Vpc_Decorator_Abstract
{
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $pc = $this->_pageCollection;

        $currentPageIds = array();
        $p = $this->_pageCollection->getCurrentPage();
        do {
            $currentPageIds[] = $p->getPageId();
        } while($p = $pc->getParentPage($p));

        $pageCollectionConfig = new Zend_Config_Ini('application/config.ini', 'pagecollection');
        foreach ($pageCollectionConfig->pagecollection->pagetypes as $type=>$i) {
            $pages = $pc->getChildPages(null, $type);
            $return['menu'][$type] = array();
            foreach ($pages as $i=>$page) {
                $data = $pc->getPageData($page);
                $class = '';
                if ($i==0) $class .= ' first';
                if ($i==sizeof($pages)-1) $class .= ' last';
                $isCurrent = in_array($page->getPageId(), $currentPageIds);
                if ($isCurrent) $class .= ' current';
                $return['menu'][$type][] = array('href'    => $data['url'],
                                                 'text'    => $data['name'],
                                                 'current' => $isCurrent,
                                                 'class'   => trim($class),
                                                 'rel'     => $data['rel']);
            }
        }

        $levels = 2;

        $level = 0;
        $page = $pc->getCurrentPage();
        while ($page && $level < $levels) {
            $pages = $pc->getChildPages($page);
            $return['submenus'][$level] = array();
            foreach ($pages as $i=>$p) {
                $data = $pc->getPageData($p);
                $class = '';
                if ($i==0) $class .= ' first';
                if ($i==sizeof($pages)-1) $class .= ' last';
                $isCurrent = in_array($page->getPageId(), $currentPageIds);
                if ($isCurrent) $class .= ' current';
                $return['submenus'][$level][] = array('href' => $data['url'],
                                                      'text' => $data['name'],
                                                      'current' => $isCurrent,
                                                      'class'   => trim($class),
                                                      'rel'  => '');
            }
            $page = $pc->getParentPage($page);
            $level--;
        }
        return $return;
    }
}
