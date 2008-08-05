<?php
class Vpc_Directories_CategoryTree_Detail_CategoryList_Component
    extends Vpc_Directories_List_Component
{
    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent;
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $ret->where('parent_id = ?', $this->getData()->parent->row->id);
        return $ret;
    }
}