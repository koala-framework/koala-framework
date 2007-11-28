<?p
/
 * @package V
 * @subpackage Decorat
 
class Vpc_Decorator_Menu_BreadCrumbs_Component extends Vpc_Decorator_Menu_Abstra

    public function getTemplateVars
   
        $return = parent::getTemplateVars(
        $pc = $this->_pageCollectio

        $pages = array(
        $page = $pc->getCurrentPage(
        while ($page)
            $pages[] = $pag
            $page = $pc->getParentPage($page
       
        $pages = array_reverse($pages
        $return['menu']['breadCrumbs'] = $this->_getMenuData($pages, array()

        return $retur
   

