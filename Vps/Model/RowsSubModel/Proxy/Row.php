<?php
class Vps_Model_RowsSubModel_Proxy_Row extends Vps_Model_Proxy_Row
    implements Vps_Model_RowsSubModel_Row_Interface
{
    public function getSubModelParentRow()
    {
        return $this->_row->getSubModelParentRow();
    }
}
