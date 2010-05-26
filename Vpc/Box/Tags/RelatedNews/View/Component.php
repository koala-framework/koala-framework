<?php
class Vpc_Box_Tags_RelatedNews_View_Component extends Vpc_Directories_List_ViewPage_Component
{
    public static function getStaticCacheVars($componentClass)
    {
        $ret = parent::getStaticCacheVars($componentClass);
        $ret[] = array(
            'model' => 'Vps_Component_Generator_Plugin_Tags_ComponentsToTagsModel'
        );
        return $ret;
    }
}
