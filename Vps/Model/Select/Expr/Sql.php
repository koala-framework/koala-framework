<?php
class Vps_Model_Select_Expr_Sql implements Vps_Model_Select_Expr_Interface
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
}
