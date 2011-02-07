<?php
class Vpc_Directories_Category_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected function _formatSelect($parentData, $select)
    {
        $select = parent::_formatSelect($parentData, $select);
        if (!$select) return null;
        return $select;
    }
}
