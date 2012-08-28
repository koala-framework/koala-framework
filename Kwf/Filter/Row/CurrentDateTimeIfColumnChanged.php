<?php
/**
 * @package Filter
 */
class Kwf_Filter_Row_CurrentDateTimeIfColumnChanged extends Kwf_Filter_Row_CurrentDateTime
{
    private $_columns;
    public function __construct(array $columns, $dateFormat = 'Y-m-d H:i:s')
    {
        $this->_columns = $columns;
        parent::__construct($dateFormat);
    }

    public function skipFilter($row, $column)
    {
        if (!$row->$column) return false;

        $dc = $row->getDirtyColumns();
        if (!array_intersect($dc, $this->_columns)) {
            return true;
        }

        return false;
    }
}
