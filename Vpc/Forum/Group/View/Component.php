<?php
class Vpc_Forum_Group_View_Component extends Vpc_Directories_List_ViewPage_Component
{
    public function getPartialCacheVars($nr)
    {
        $ret = parent::getPartialCacheVars($nr);
        $ret[] = array(
            'model' => 'Vpc_Posts_Directory_Model',
            'id' => null
        );
        return $ret;
    }
}
