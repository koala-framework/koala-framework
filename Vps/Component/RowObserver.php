<?php
class Vps_Component_RowObserver
{
    static private $_instance;
    private $_process = array(
        'insert' => array(),
        'update' => array(),
        'delete' => array(),
        'save'   => array()
    );
    private $_skipFnF = true;
    private $_processed = array();

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    //für tests
    public function setSkipFnF($v)
    {
        $this->_skipFnF = $v;
    }

    public function clear()
    {
        foreach (array_keys($this->_process) as $i) {
            $this->_process[$i] = array();
        }
        $this->_processed = array();
    }


    public function insert($row)
    {
        $this->_process['insert'][] = $row;
    }

    public function update($row)
    {
        $this->_process['update'][] = $row;
    }

    public function save($row)
    {
        $this->_process['save'][] = $row;
    }

    public function delete($row)
    {
        // Wird hier direkt aufgerufen, weil wenn später aufgerufen, ist row schon gelöscht
        if (!Vps_Component_Data_Root::getComponentClass()) return;
        $this->_processCache($row);
    }

    protected function _processCache($row)
    {
        if ($row instanceof Zend_Db_Table_Row_Abstract) {
            $model = $row->getTable();
            $primary = current($model->info('primary'));
        } else {
            $model = $row->getModel();
            $primary = $model->getPrimaryKey();
            if (get_class($model) == 'Vps_Model_Db') $model = $model->getTable();
            if ($model instanceof Vps_Component_Cache_MetaModel ||
                $model instanceof Vps_Component_Cache_Model
            ) {
                return false;
            }
        }
        if (get_class($model) == 'Vps_Db_Table') return false;
        if ($this->_skipFnF) {
            $m = $model;
            while ($m instanceof Vps_Model_Proxy) {
                $m = $m->getProxyModel();
            }
            if ($m instanceof Vps_Model_FnF) return false;
        }
        $id = $row->$primary;
        $componentId = isset($row->component_id) ? $row->component_id : null;
        $modelname = get_class($model);
        if (!isset($this->_processed[$modelname][$id])) {
            $this->_processed[$modelname][$id] = true;
            Vps_Component_Cache::getInstance()->clean(
                Vps_Component_Cache::CLEANING_MODE_META, $row
            );
            return true;
        }
        return false;
    }

    public function process()
    {
        // View Cache
        if (!Vps_Component_Data_Root::getComponentClass()) return;
        foreach ($this->_process as $action => $process) {
            foreach (array_reverse($process) as $row) {
                $this->_processCache($row);
            }
        }
        // Suchindex
        Vps_Dao_Index::process();
    }
}
