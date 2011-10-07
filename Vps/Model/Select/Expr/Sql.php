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

    public function toArray()
    {
        $field = $this->_field;
        if ($field instanceof Vps_Model_Select_Expr_Interface) $field = $field->toArray();
        return array(
            'exprType' => str_replace('Vps_Model_Select_Expr_', '', get_class($this)),
            'sql' => $this->_sql,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Vps_Model_Select_Expr_'.$data['exprType'];
        return new $cls($data['sql']);
    }
}
