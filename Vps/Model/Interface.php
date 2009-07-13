<?php
interface Vps_Model_Interface
{
    const FORMAT_SQL = 'sql';
    const FORMAT_ARRAY = 'array';

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
    public function getExprValue(Vps_Model_Row_Interface $row, $name);

    public function getRow($id);
    public function getRows($where=null, $order=null, $limit=null, $start=null);
    public function countRows($where = array());

    public function getSupportedImportExportFormats();
    public function copyDataFromModel(Vps_Model_Interface $sourceModel, $select = null, $format = null);
    public function export($format, $select = array());
    public function import($format, $data, $options = array());
    public function writeBuffer();

    public function deleteRows($where);

    //deprecated
    public function find($id);
    public function fetchAll($where=null, $order=null, $limit=null, $start=null);
    public function fetchCount($where = array());


}
