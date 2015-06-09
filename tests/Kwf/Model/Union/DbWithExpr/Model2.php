<?php
class Kwf_Model_Union_DbWithExpr_Model2 extends Kwf_Model_Union_Db_Model2
{
    protected function _init()
    {
        $this->_exprs['cc'] = new Kwf_Model_Select_Expr_String('foobar');
        $this->_columnMappings['Kwf_Model_Union_Db_TestMapping']['baz'] = 'cc';
        parent::_init();
    }
}

