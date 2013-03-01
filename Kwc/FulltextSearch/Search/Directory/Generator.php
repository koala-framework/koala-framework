<?php
class Kwc_FulltextSearch_Search_Directory_Generator extends Kwf_Component_Generator_Table
{
    protected $_hasNumericIds = false;

    protected function _formatSelect($parentData, $select)
    {
        $select = parent::_formatSelect($parentData, $select);
        if (!$parentData) {
            throw new Kwf_Exception('ParentData is null, this should not happen');
        }
        $select->whereEquals('subroot', $parentData);
        return $select;
    }
}
