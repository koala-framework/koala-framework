<?php
class Vps_Index_Vpc
{
    public static function update($componentId)
    {
        //TODO auf TreeCache aktuallisieren
        /*
        $pc = Vps_PageCollection_Abstract::getInstance();
        $page = $pc->getPageById($componentId);
        $searchVars = $page->getSearchVars();
        $t = new Vps_Dao_Index();
        $row = $t->find($page->getId())->current();
        if (!$row) {
            $row = $t->createRow();
            $row->component_id = $page->getId();
        }
        $row->text = trim($searchVars['text']);
        $row->save();
        */
    }
    
    public static function clearCache($componentId)
    {
        $smarty = new Vps_View_Smarty_Cached();
        $template = VPS_PATH . '/views/site.html';
        $smarty->getSmarty()->clear_cache($template, $componentId);
    }
}
