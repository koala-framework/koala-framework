<?php
class Vpc_Directories_CategoryTree_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected $_nameColumn = 'name';

    public function select($parentData, array $constraints = array())
    {
        $select = parent::select($parentData, $constraints);
        if (!$select) return null;
        return $select;
    }
}
