<?php
class Vpc_Advanced_Amazon_Nodes_Detail_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Advanced_Amazon_Nodes_ProductsDirectory_View_Component';
        return $ret;
    }
    protected function _getItemDirectory()
    {
        return $this->getData()->parent->getChildComponent('_products');
    }
    public function getSelect()
    {
        $select = parent::getSelect();
        if (!$select) return $select;

        $select->whereEquals('BrowseNode', $this->getData()->row->node_id);

        return $select;
    }
}
