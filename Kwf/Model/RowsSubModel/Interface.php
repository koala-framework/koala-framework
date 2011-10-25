<?php
interface Kwf_Model_RowsSubModel_Interface
{
    const SUBMODEL_PARENT = 'submodelParent';

    public function getRowsByParentRow(Kwf_Model_Row_Interface $parentRow, $select = array());
    public function createRowByParentRow(Kwf_Model_Row_Interface $parentRow, array $data = array());
}
