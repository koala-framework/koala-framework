<?php
class Vpc_Advanced_Amazon_Nodes_ProductsDirectory_Generator extends Vps_Component_Generator_Page_Table
{
    protected $_loadTableFromComponent = false;
    protected $_idColumn = 'asin';

    protected function _formatSelect($parentData, $select)
    {
        $select = parent::_formatSelect($parentData, $select);
        if (!$select) return $select;
        $select->whereEquals('SearchIndex', 'Books');
        $tag = $parentData->parent->getComponent()->getRow()->associate_tag;
        if ($tag) {
            $select->whereEquals('AssociateTag', $tag);
        }
        return $select;
    }
}
