<?php
class Vps_Filter_Row_CurrentDateTimeIfColumnChanged extends Vps_Filter_Row_CurrentDateTime
{
    private $_columns;
    public function __construct(array $columns, $dateFormat = 'Y-m-d H:i:s')
    {
        $this->_columns = $columns;
        parent::__construct($dateFormat);
    }

    public function skipFilter($row)
    {
        //TODO: $row->getDirtyColumns direkt verwenden!
        while ($row instanceof Vps_Model_Proxy_Row) $row = $row->getProxiedRow();
        if (!$row instanceof Vps_Model_Db_Row) return false;
        $dc = $row->getRow()->___getDirtyColumns();
        if (!array_intersect($dc, $this->_columns)) {
            return true;
        }

        return false;
    }
}
