<?php
class Vps_Component_Decorator_Menu extends Vps_Component_Decorator_Abstract
{
    var $_pageCollection = null;
    
    public function __construct(Vps_Dao $dao, Vps_Component_Interface $component, Vps_PageCollection_Abstract $pageCollection = null)
    {
        $this->_pageCollection = $pageCollection;
        parent::__construct($dao, $component);
    }

    public function getTemplateVars($mode)
    {
        $pc = $this->_pageCollection;

        // Erste Ebene
        $menus = array();
        $pages = $pc->getChildPages($pc->getRootPage());
        array_unshift($pages, $pc->getRootPage());
        foreach ($pages as $page) {
            $data = $pc->getPageData($page);
            $menus[$data['path']] = $data['sitename'];
        }

        $return = parent::getTemplateVars($mode);
        foreach ($menus as $url=>$text) {
            $return['menu'][] = array('url'=>$url, 'text'=>$text);
        }
        return $return;
    }
}