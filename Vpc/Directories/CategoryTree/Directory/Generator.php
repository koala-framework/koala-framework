<?php
class Vpc_Directories_CategoryTree_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected $_filenameColumn = 'name_path';

    protected function _formatSelect($parentData, array $constraints = array())
    {
        $select = parent::_formatSelect($parentData, $constraints);
        if (!$select) return null;
        return $select;
    }
}
