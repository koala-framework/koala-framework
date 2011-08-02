<?php
class Vps_Filter_Row_CurrentDateTimeIfColumnChanged extends Vps_Filter_Row_CurrentDateTime
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

        //TODO: $row->getDirtyColumns direkt verwenden!
        //TODO: wenn darauf umgestellt wird, testen ob die siblings eh auch mit dabei sind
        //      (bsp rssinclude Users premium_until)
        $rows = array();
        $rows[] = $row;
        foreach ($row->_getSiblingRows() as $row) {
            $rows[] = $row;
        }
        while ($row instanceof Vps_Model_Proxy_Row) {
            $row = $row->getProxiedRow();
            foreach ($row->_getSiblingRows() as $row) {
                $rows[] = $row;
            }
        }
        $dc = array();
        foreach ($rows as $r) {
            if ($r instanceof Vps_Model_Db_Row) {
                $dc = array_merge($dc, $row->getRow()->___getDirtyColumns());
            }
        }
        if (!array_intersect($dc, $this->_columns)) {
            return true;
        }

        return false;
    }
}
