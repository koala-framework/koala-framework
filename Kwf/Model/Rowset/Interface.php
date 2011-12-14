<?php
/**
 * @package Model
 * @subpackage Interface
 */
interface Kwf_Model_Rowset_Interface extends SeekableIterator, Countable
{
    public function getModel();
}
