<?php
class Kwc_Directories_CategoryTree_Detail_CategoryList_Component
    extends Kwc_Directories_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['view'] = 'Kwc_Directories_CategoryTree_View_Component';
        $ret['useDirectorySelect'] = false;
        return $ret;
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        return self::_getParentItemDirectoryClasses($directoryClass, 1);
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