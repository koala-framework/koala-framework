<?php
class Kwc_Root_Category_Update_4 extends Kwf_Update
{
    public function postUpdate()
    {
        $pages = Kwf_Registry::get('db')
            ->query("SELECT id, parent_id FROM kwf_pages");
        $parentIds = array();
        while($page = $pages->fetch()) {
            $parentIds[$page['id']] = $page['parent_id'];
        }
        $subroots = array();
        foreach (array_keys($parentIds) as $pageId) {
            $parent = $pageId;
            while (isset($parentIds[$parent])) {
                if ($parent == $parentIds[$parent]) {
                    //endless loop
                    continue 2;
                }
                $parent = $parentIds[$parent];
            }
            if (is_numeric($parent)) {
                //unused
                continue;
            }
            if (!isset($subroots[$parent])) {
                $c = Kwf_Component_Data_Root::getInstance()->getComponentById($parent);
                if ($c) {
                    $subroots[$parent] = $c->getSubroot()->componentId;
                } else {
                    $subroots[$parent] = false;
                }
            }
            if ($subroots[$parent]) {
                $db = Kwf_Registry::get('db');
                $db->query("UPDATE kwf_pages SET parent_subroot_id=".$db->quote($subroots[$parent]).' WHERE id='.$db->quote($pageId));
            }
        }
    }
}


