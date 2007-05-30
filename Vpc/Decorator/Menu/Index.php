<?php
class Vpc_Decorator_Menu_Index extends Vpc_Decorator_Abstract
{
    var $_pageCollection = null;
    
    public function __construct(Vps_Dao $dao, Vpc_Interface $component, Vps_PageCollection_Abstract $pageCollection = null)
    {
        $this->_pageCollection = $pageCollection;
        parent::__construct($dao, $component);
    }

    public function getTemplateVars($mode)
    {
        $return = parent::getTemplateVars($mode);
        $pc = $this->_pageCollection;

        $startingPage = $pc->getRootPage();
        $levels = 2;

        $menus = array();
        $pages = $pc->getChildPages($startingPage);
        array_unshift($pages, $startingPage);
        foreach ($pages as $page) {
            $data = $pc->getPageData($page);
            $return['menu'][] = array('url' => $data['path'], 'text' => $data['name']);
        }

        return $return;
    }
    
}