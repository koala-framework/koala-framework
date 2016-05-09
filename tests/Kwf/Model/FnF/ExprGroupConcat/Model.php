<?php

class Kwf_Model_FnF_ExprGroupConcat_Model extends Kwf_Model_FnF
{
    public function _init()
    {
        $this->_dependentModels['Children'] = 'Kwf_Model_FnF_ExprGroupConcat_ChildModel';
        $this->_columns = array('id', 'foo');
        parent::_init();
    }

    public function __construct($config = array())
    {
        $config['data'] = array(
            array('id' => 1),
            array('id' => 2),
            array('id' => 3)
        );

        $s = new Kwf_Model_Select();
        $config['exprs'] = array(
            'foo1' => new Kwf_Model_Select_Expr_Child_GroupConcat('Children', 'id'),
            'foo2' => new Kwf_Model_Select_Expr_Child_GroupConcat('Children', 'id', ', '),
            'foo3' => new Kwf_Model_Select_Expr_Child_GroupConcat('Children', 'id', ', ', $s, 'sort_field'),
            'foo4' => new Kwf_Model_Select_Expr_Child_GroupConcat('Children', 'id', ', ', $s, array(
                'field' => 'sort_field',
                'direction' => 'DESC'
            )),
        );

        parent::__construct($config);
    }
}
