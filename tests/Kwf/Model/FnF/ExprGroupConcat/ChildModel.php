<?php
class Kwf_Model_FnF_ExprGroupConcat_ChildModel extends Kwf_Model_FnF
{
    public function _init()
    {
        $this->_referenceMap = array(
            'Parent' => 'parent_id->Kwf_Model_FnF_ExprGroupConcat_Model'
        );
        $this->_columns = array('id', 'parent_id', 'sort_field', 'sort_field_2');
        $this->_data = array(
            array('id' => 1, 'parent_id' => 1, 'sort_field'=> 3, 'sort_field_2'=>'bbb'),
            array('id' => 2, 'parent_id' => 1, 'sort_field'=> 1, 'sort_field_2'=>'aaa'),
            array('id' => 3, 'parent_id' => 2, 'sort_field'=> 2, 'sort_field_2'=>'ccc')
        );

        parent::_init();
    }
}

