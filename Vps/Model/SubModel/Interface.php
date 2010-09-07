<?php
interface Vps_Model_SubModel_Interface
{
    const SUBMODEL_PARENT = 'submodelParent';

    public function getRowBySiblingRow(Vps_Model_Row_Interface $siblingRow);
}
