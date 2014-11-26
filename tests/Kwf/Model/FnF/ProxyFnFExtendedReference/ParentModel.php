<?php
class Kwf_Model_FnF_ProxyFnFExtendedReference_ParentModel extends Kwf_Model_FnF
{
    public function _init()
    {
        $this->_dependentModels['Childs'] = 'Kwf_Model_FnF_ProxyFnFExtendedReference_ChildModel';
        $this->_columns = array('id', 'foo');
        $this->_data =  array(
                array('id' => 1, 'foo'=> 5),
                array('id' => 2, 'foo'=> 7)
        );
        parent::_init();
    }
}
