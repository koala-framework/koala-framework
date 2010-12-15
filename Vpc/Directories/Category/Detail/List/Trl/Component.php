<?php
class Vpc_Directories_Category_Detail_List_Trl_Component extends Vpc_Directories_List_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $s = new Vps_Model_Select();
        $s->whereEquals('category_id', $this->getData()->parent->id);
        $select->where(new Vps_Model_Select_Expr_Child_Contains('Categories', $s));
        return $select;
    }
}
