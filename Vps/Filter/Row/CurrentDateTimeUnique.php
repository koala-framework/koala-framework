<?php
/**
 * Generates the current date for a model, but makes it unique.
 * If a row already exists with the current date, it adds one second.
 * Is used for instance in the service
 */
class Vps_Filter_Row_CurrentDateTimeUnique extends Vps_Filter_Row_CurrentDateTime
{
    public function filter($row)
    {
        $ret = parent::filter($row);

        $m = $row->getModel();
        $highestRow = $m->getRow($m->select()
            ->where(new Vps_Model_Select_Expr_Or(array(
                new Vps_Model_Select_Expr_Higher($this->_field, new Vps_DateTime($ret)),
                new Vps_Model_Select_Expr_Equal($this->_field, $ret)
            )))
            ->order($this->_field, 'DESC')
        );
        if ($highestRow) {
            $ret = date($this->_dateFormat, strtotime($highestRow->{$this->_field}) + 1);
        }
        return $ret;
    }
}
