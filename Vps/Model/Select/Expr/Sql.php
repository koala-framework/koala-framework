<?php
class Vps_Model_Select_Expr_Sql implements Vps_Model_Select_Expr_Interface
{
    private $_sql;

    public function __construct($sql)
    {
        $this->_sql = (string)$sql;
    }

    public function getSql()
    {
        return $this->_sql;
    }

    public function validate()
    {
    }

    public function getResultType()
    {
        return null;
    }
}
