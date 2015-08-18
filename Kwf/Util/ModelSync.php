<?php
class Kwf_Util_ModelSync
{
    protected $_model;
    protected $_compareColumns;
    protected $_lastSyncStat = null;
    protected $_lastSyncMapping = null;

    public function __construct(Kwf_Model_Abstract $model, array $compareColumns)
    {
        $this->_model = $model;
        $this->_compareColumns = $compareColumns;
    }

    public function syncData(array $data, Kwf_Model_Select $select = null)
    {
        $this->_lastSyncMapping = array();
        $this->_lastSyncStat = array(
            'check' => 0,
            'update' => 0,
            'create' => 0,
            'delete' => 0,
        );
        if (!$select) $select = new Kwf_Model_Select();
        $existingRows = array();
        foreach ($this->_model->getRows($select) as $row) {
            $keyValues = array();
            foreach ($this->_compareColumns as $column) {
                $keyValues[] = $row->$column;
            }
            $key = implode('#', $keyValues);
            $existingRows[$key] = $row;
        }
        $this->_lastSyncStat['check'] = count($existingRows);
        foreach ($data as $id => $d) {
            $keyValues = array();
            foreach ($this->_compareColumns as $column) {
                $keyValues[] = $d[$column];
            }
            $key = implode('#', $keyValues);
            $row = isset($existingRows[$key]) ? $existingRows[$key] : null;
            if ($row) {
                foreach ($d as $k => $v) {
                    $row->$k = $v;
                }
                if ($row->isDirty()) {
                    $this->_lastSyncStat['update']++;
                    $row->save();
                }
                unset($existingRows[$key]);
            } else {
                $this->_lastSyncStat['create']++;
                $row = $this->_model->createRow($d);
                $row->save();
            }
            $this->_lastSyncMapping[$id] = $row->id;
        }
        foreach ($existingRows as $row) {
            $this->_lastSyncStat['delete']++;
            $row->delete();
        }
        return true;
    }

    /**
     * Syncs given array with given rowset
     *
     * @param mixed Model
     * @param array() Name of columns which are used to compare the syncing rows
     * @param array Data to sync
     * @param mixed Which rows are to be considered for syncing
     * @return array Mapping of synced rows ("key of import array" => "id of synced row")
     */
    public static function sync(Kwf_Model_Abstract $model, array $compareColumns, array $data, Kwf_Model_Select $select = null)
    {
        $sync = new Kwf_Util_ModelSync($model, $compareColumns);
        $sync->syncData($data, $select);
        return $sync->getMappingForLastSync();
    }

    public function getMappingForLastSync()
    {
        if (is_null($this->_lastSyncMapping)) throw new Kwf_Exception('There was no last sync.');
        return $this->_lastSyncMapping;
    }

    public function getStatForLastSync()
    {
        if (is_null($this->_lastSyncStat)) throw new Kwf_Exception('There was no last sync.');
        return $this->_lastSyncStat;
    }
}
