<?php
/**
 * @package Model
 * @subpackage Iterator
 */
interface Kwf_Model_Iterator_ModelIterator_Interface extends Iterator
{
    public function getSelect();
    public function getModel();
}
