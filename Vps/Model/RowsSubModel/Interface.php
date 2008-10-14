<?php
interface Vps_Model_RowsSubModel_Interface
{
    public function getRowsByParentRow(Vps_Model_Row_Interface $parentRow, $select = array());
}
