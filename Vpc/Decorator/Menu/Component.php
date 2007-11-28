<?p
/
 * Menüdecorator. Speichert in Template-Variable alle Werte, d
 * für das Menü benötigt werde
 * @package V
 * @subpackage Decorat
 
class Vpc_Decorator_Menu_Component extends Vpc_Decorator_Menu_Abstra

    protected $_levels = 
  
    public function getTemplateVars
   
        $return = parent::getTemplateVars(
        $pc = $this->getPageCollection(

        // Array mit IDs von aktueller Seiten und Parent Pag
        $currentPageIds = array(
        $p = $pc->getCurrentPage(
        do
            $currentPageIds[] = $p->getPageId(
        } while($p = $pc->getParentPage($p)

        // Hauptmen
        $config = new Zend_Config_Ini('application/config.ini', 'pagecollection'
        foreach ($config->pagecollection->pagetypes as $type => $i)
            $pages = $pc->getChildPages(null, $type
            $return['menu'][$type] = array(
            $return['menu'][$type] = $this->_getMenuData($pages, $currentPageIds
       

        // Submen�
        $level = 
        $page = $pc->findPage(array_pop($currentPageIds)
        while ($page && $level < $this->_levels)
            $pages = $pc->getChildPages($page
            $return['submenus'][$level] = $this->_getMenuData($pages, $currentPageIds
            $page = $pc->findPage(array_pop($currentPageIds)
            $level+
       
        return $retur
   


