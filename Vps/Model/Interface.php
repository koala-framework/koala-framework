<?php
interface Vps_Model_Interface
{
    public function createRow(array $data=array());
    public function find($id);
    public function fetchAll($where=null, $order=null, $limit=null, $start=null);
    public function fetchCount($where = array());
    public function getPrimaryKey();
}
