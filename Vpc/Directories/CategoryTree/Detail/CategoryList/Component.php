<?php
class Vpc_Directories_CategoryTree_Detail_CategoryList_Component
    extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Directories_CategoryTree_View_Component';
        $ret['useDirectorySelect'] = false;
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent;
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $ret->whereEquals('parent_id', $this->getData()->parent->row->id);
        return $ret;
    }
}