<?php
/**
 * Menüdecorator. Speichert in Template-Variable alle Werte, die 
 * für das Menü benötigt werden.
 * @package Vpc
 * @subpackage Decorator
 */
class Vpc_Decorator_Menu_Index extends Vpc_Decorator_Abstract
{
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $pc = $this->_pageCollection;
        $rootPage = $pc->getRootPage();

        $menus = array();
        $pages = $pc->getChildPages($rootPage);
        array_unshift($pages, $rootPage);
        $return['menu']['menu'] = array();
        foreach ($pages as $page) {
            $data = $pc->getPageData($page);
            $return['menu']['menu'][] = array('url' => $data['url'], 'text' => $data['name']);
        }
        
        $page = $pc->getCurrentPage();
        $return['menu']['breadcrumbs'] = array();
        while ($page) {
            $url = $pc->getUrl($page);
            $text = $pc->getFilename($page);
            if ($page === $rootPage && $text == '') {
                $text = 'home';
            }
            $return['menu']['breadcrumbs'][] = array('url' => $url, 'text' => $text);
            $page = $pc->getParentPage($page);
        }
        $return['menu']['breadcrumbs'] = array_reverse($return['menu']['breadcrumbs']);

        return $return;
    }
    
}