<?php
class Vpc_Directories_CategoryTree_View_Component
    extends Vpc_Directories_Category_View_Component
{
    protected function _getCountCategoryIds($item)
    {
        return $item->row->getRow()->getRecursiveChildCategoryIds(array(
            'visible = 1'
        ));
    }
}

