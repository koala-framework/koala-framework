<?php
interface Vps_Model_Tree_Row_Interface extends Vps_Model_Row_Interface
{
    public function getParentNode();
    public function getChildNodes($select = array());
}
