<?php
interface Kwf_Model_Tree_Row_Interface extends Kwf_Model_Row_Interface
{
    public function getParentNode();
    public function getChildNodes($select = array());
}
