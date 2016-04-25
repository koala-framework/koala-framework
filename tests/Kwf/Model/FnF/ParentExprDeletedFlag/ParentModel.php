<?php
class Kwf_Model_FnF_ParentExprDeletedFlag_ParentModel extends Kwf_Model_FnF
{
    public function _init()
    {
        $this->_dependentModels['Childs'] = 'Kwf_Model_FnF_ParentExprDeletedFlag_ChildModel';
        $this->_hasDeletedFlag = true;
        $this->_columns = array('id', 'foo', 'deleted');
        $this->_data =  array(
                array('id' => 1, 'foo'=> 5, 'deleted' => true),
                array('id' => 2, 'foo'=> 7, 'deleted' => false)
        );
        parent::_init();
    }
}
