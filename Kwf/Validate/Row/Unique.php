<?php
class Vps_Validate_Row_Unique extends Vps_Validate_Row_Abstract
{
    const NOT_UNIQUE = 'notUnique';

    private $_addedExprs = array();

    public function __construct()
    {
        $this->_messageTemplates[self::NOT_UNIQUE] = trlVps("'%value%' does already exist");
    }

    public function addSelectExpr(Vps_Model_Select_Expr_Interface $expr)
    {
        $this->_addedExprs[] = $expr;
    }

    public function isValidRow($value, $row)
    {
        $valueString = (string)$value;
        $this->_setValue($valueString);

        $select = $row->getModel()->select()
            ->whereEquals($this->_field, $valueString);
        foreach ($this->_addedExprs as $expr) {
            $select->where($expr);
        }
        $primaryKey = $row->getModel()->getPrimaryKey();
        if ($row->$primaryKey) {
            $select->whereNotEquals($primaryKey, $row->$primaryKey);
        }
        if ($row->getModel()->countRows($select)) {
            $this->_error(self::NOT_UNIQUE);
            return false;
        }

        return true;
    }
}
