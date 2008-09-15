<?php
class Vpc_Directories_Item_Detail_AssignedCategories_View_Component
    extends Vpc_Directories_List_ViewPage_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['paging'] = false;
        // deaktiviert wg. cache löschen $ret['viewCache'] = true;
        // muss ich web aktiviert werden und eigene Admin-datei erstellt werden die cache korrekt löscht
        return $ret;
    }
}
