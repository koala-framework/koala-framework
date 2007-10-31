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

        $menus = array();
        $pages = $pc->getChildPages();
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
            $return['menu']['breadcrumbs'][] = array('url' => $url, 'text' => $text);
            $page = $pc->getParentPage($page);
        }
        $return['menu']['breadcrumbs'] = array_reverse($return['menu']['breadcrumbs']);

        return $return;
    }
    
}