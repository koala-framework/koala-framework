<?php
class Vpc_Forum_Group_View_Component extends Vpc_Directories_List_ViewPage_Component
{
    public static function getStaticCacheVars()
    {
        $ret = array();
        $ret[] = array(
            'model' => 'Vpc_Posts_Directory_Model'
        );
        return $ret;
    }
}
