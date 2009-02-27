<?php
interface Vps_Model_Interface
{
    public function createRow(array $data=array());
    public function getPrimaryKey();
    public function isEqual(Vps_Model_Interface $other);
    public function select($where = array());
    public function getColumns();
    public function hasColumn($col);
    public function getSiblingModels();
    public function setDefault(array $default);
    /**
     * @return string
     */
    public function getUniqueIdentifier();

    public function getRow($id);
    public function getRows($where=null, $order=null, $limit=null, $start=null);
    public function countRows($where = array());
    public function deleteRows($where);

    //deprecated
    public function find($id);
    public function fetchAll($where=null, $order=null, $limit=null, $start=null);
    public function fetchCount($where = array());


}
