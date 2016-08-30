<?php
class Kwc_Directories_List_ViewGrouping_Component extends Kwc_Directories_Item_DirectoryNoAdmin_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['detail']['component'] = 'Kwc_Directories_List_ViewGrouping_Group_Component';
        $ret['dependentModel'] = false;
        return $ret;
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $select = $this->getData()->parent->getGenerator('detail')->getFormattedSelect($this->getData()->parent);
        if (!$this->_getSetting('dependentModel')) throw new Kwf_Exception('Set dependentModel for correct grouping');
        $ret->where(new Kwf_Model_Select_Expr_Child_Contains($this->_getSetting('dependentModel'), $select));
        $ret->order('pos', 'ASC');
        return $ret;
    }
}
