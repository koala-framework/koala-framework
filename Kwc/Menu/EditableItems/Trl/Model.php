<?php
// Only used for Controller as Generator creates components from master
class Kwc_Menu_EditableItems_Trl_Model extends Kwc_Menu_EditableItems_Model
{
    protected function _getIdForPage($page)
    {
        return $page->id;
    }
}
