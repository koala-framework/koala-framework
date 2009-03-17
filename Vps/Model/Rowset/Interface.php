<?php
interface Vps_Model_Rowset_Interface extends SeekableIterator, Countable
{
    public function getModel();
}
