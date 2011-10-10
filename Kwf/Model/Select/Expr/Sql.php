<?php
class Kwf_Model_Select_Expr_Sql implements Kwf_Model_Select_Expr_Interface
{
    private $_sql;
    private $_usedColumns;

    /**
     * @param string die sql abfrage die 1:1 verwendet wird
     * @param array (optional) felder die in der sql abfrage verwendet werden.
     *              Kann verwendet werden um bei sibling models ein joinen der sibling tabelle zu erreichen
     *              was ja nicht automatisch passieren kann da das model nicht weiß, dass spalten aus dem
     *              sibling verwendet werden
     */
    public function __construct($sql, array $usedColumns = array())
    {
        $this->_sql = (string)$sql;
        $this->_usedColumns = $usedColumns;
    }

    public function getSql()
    {
        return $this->_sql;
    }

    public function getUsedColumns()
    {
        return $this->_usedColumns;
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
        if ($field instanceof Kwf_Model_Select_Expr_Interface) $field = $field->toArray();
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'sql' => $this->_sql,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        return new $cls($data['sql']);
    }
}
