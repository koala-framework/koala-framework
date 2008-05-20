<?php
class Vps_Index_Vpc
{
    public static function update($componentId)
    {
        //TODO auf TreeCache aktuallisieren
        /*
        $pc = Vps_PageCollection_TreeBase::getInstance();
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
}
