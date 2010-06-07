<?php
class Vpc_Root_Category_Trl_FilenameFilter extends Vpc_Root_Category_FilenameFilter
{
    protected function _getComponentId($row)
    {
        return $row->component_id;
    }
}
