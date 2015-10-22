<?php
class Kwc_Directories_Category_Detail_List_Trl_Component extends Kwc_Directories_List_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $s = new Kwf_Model_Select();
        $s->whereEquals('category_id', $this->getData()->parent->id);
        $select->where(new Kwf_Model_Select_Expr_Child_Contains('Categories', $s));
        return $select;
    }
}
