<?php
/**
 * @package Model
 * @subpackage Interface
 */
interface Kwf_Model_Interface
{
    const FORMAT_SQL = 'sql';
    const FORMAT_ARRAY = 'array';
    const FORMAT_CSV = 'csv';

    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INTEGER = 'int';
    const TYPE_STRING = 'string';
    const TYPE_FLOAT = 'float';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';

    /**
     * @return Kwf_Model_Row_Interface
     */
    public function createRow(array $data=array());
    public function getPrimaryKey();
    public function isEqual(Kwf_Model_Interface $other);
    /**
     * @return Kwf_Model_Select
     */
    public function select($where = array());
    public function getColumnType($col);
    public function getColumns();
    public function hasColumn($col);
    public function getSiblingModels();
    public function setDefault(array $default);
    /**
     * @return string
     */
    public function getUniqueIdentifier();
    public function getExprValue($row, $name);

    /**
     * @return Kwf_Model_Row_Interface
     */
    public function getRow($id);
    public function getRows($where=null, $order=null, $limit=null, $start=null);
    public function getIds($where=null, $order=null, $limit=null, $start=null);
    public function countRows($where = array());

    public function getSupportedImportExportFormats();
    public function copyDataFromModel(Kwf_Model_Interface $sourceModel, Kwf_Model_Select $select = null, array $importOptions = array());
    public function export($format, $select = array(), $options = array());
    public function import($format, $data, $options = array());
    public function writeBuffer();
    public function updateRow(array $data);
    public function insertRow(array $data);

    public function deleteRows($where);
    public function updateRows($data, $where); // in db-model werden rows umgangen (kein aftersave...), in data-model nicht

    public function callMultiple(array $call);

    public function fetchColumnByPrimaryId($column, $id);
    public function fetchColumnsByPrimaryId(array $columns, $id);

    public function getColumnMapping($mapping, $column);
    public function getColumnMappings($mapping);

    public function getEventSubscribers();
    public function getUpdates();

    public function getSerializationColumns($groups);


    //deprecated
    public function find($id);
    public function fetchAll($where=null, $order=null, $limit=null, $start=null);
    public function fetchCount($where = array());


}
