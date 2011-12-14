<?php
/**
 * @package Model
 * @subpackage Interface
 */
interface Kwf_Model_SubModel_Interface
{
    public function getRowBySiblingRow(Kwf_Model_Row_Interface $siblingRow);
}
