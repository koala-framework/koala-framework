<?php
class Kwf_Model_RowsSubModel_Proxy_Row extends Kwf_Model_Proxy_Row
    implements Kwf_Model_RowsSubModel_Row_Interface
{
    public function getSubModelParentRow()
    {
        return $this->_row->getSubModelParentRow();
    }
}
