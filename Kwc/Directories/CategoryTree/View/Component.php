<?php
class Kwc_Directories_CategoryTree_View_Component
    extends Kwc_Directories_Category_View_Component
{
    private $_viewComponents = array();

    protected function _getCountCategoryIds($item)
    {
        return $item->row->getRecursiveChildCategoryIds();
    }
}

