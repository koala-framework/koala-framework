<?php
class Kwf_Model_FnF_ParentExprDeletedFlag_ChildModel extends Kwf_Model_FnF
{
    public function _init()
    {
        $this->_referenceMap = array(
            'Parent' => 'parent_id->Kwf_Model_FnF_ParentExprDeletedFlag_ParentModel'
        );
        $this->_columns = array('id', 'parent_id', 'bar');
        $this->_data = array(
                array('id' => 1, 'parent_id' => 1, 'bar'=> 5),
                array('id' => 2, 'parent_id' => 2, 'bar'=> 1),
                array('id' => 3, 'parent_id' => 1, 'bar'=> 1),
        );

        parent::_init();
        $this->_exprs['parent_foo'] = new Kwf_Model_Select_Expr_Parent('Parent', 'foo');
    }
}
