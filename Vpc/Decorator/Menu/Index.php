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
        $rootPage = $pc->getRootPage();

        $menus = array();
        $pages = $pc->getChildPages($rootPage);
        array_unshift($pages, $rootPage);
        $return['menu']['menu'] = array();
        foreach ($pages as $page) {
            $data = $pc->getPageData($page);
            $return['menu']['menu'][] = array('url' => $data['path'], 'text' => $data['name']);
        }
        
        $page = $pc->getCurrentPage();
        $return['menu']['breadcrumbs'] = array();
        while ($page) {
            $path = $pc->getPath($page);
            $text = $pc->getFilename($page);
            if ($page === $rootPage && $text == '') {
                $text = 'home';
            }
            $return['menu']['breadcrumbs'][] = array('url' => $path, 'text' => $text);
            $page = $pc->getParentPage($page);
        }
        $return['menu']['breadcrumbs'] = array_reverse($return['menu']['breadcrumbs']);

        return $return;
    }
    
}