<?php
class Kwc_Root_Category_Trl_FilenameFilter extends Kwc_Root_Category_FilenameFilter
{
    protected function _getComponentId($row)
    {
        return $row->component_id;
    }
}
