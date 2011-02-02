<?php
class Vps_Component_ModelObserver
{
    /**
     * @var Vps_Component_ModelObserver
     */
    static private $_instance;
    private $_process = array(
        'insert' => array(),
        'update' => array(),
        'delete' => array(),
        'save'   => array()
    );
    private $_skipFnF = true;
    private $_processed = array();
    private $_enabled = true; // wird zB. beim Import in Proxy ausgeschaltet
    private $_enableProcess = true; // für Unit Tests

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function clearInstance()
    {
        self::$_instance = null;
    }

    //für tests
    public function setSkipFnF($v)
    {
        $this->_skipFnF = $v;
    }

    public function enable()
    {
        $this->_enabled = true;
    }

    public function disable()
    {
        $this->_enabled = false;
    }

    public function setEnableProcess($enableProcess)
    {
        $this->_enableProcess = $enableProcess;
    }

    public function clear()
    {
        foreach (array_keys($this->_process) as $i) {
            $this->_process[$i] = array();
        }
        $this->_processed = array();
    }


    public function add($function, $source)
    {
        if ($this->_enabled) {
            $source = array('source' => $source);
            if ($function == 'delete') {
                // Wird hier direkt aufgerufen, weil wenn später aufgerufen, ist row schon gelöscht
                if (!Vps_Component_Data_Root::getComponentClass()) return;
                if ($this->_enabled) $this->_processCache($source);
            } else {
                if ($source['source'] instanceof Vps_Model_Row_Abstract) {
                    $source['dirtyColumns'] = $source['source']->getDirtyColumns();
                }
                $this->_process[$function][] = $source;
            }
        }
    }

    protected function _processCache($source)
    {
        if ($source['source'] instanceof Vps_Model_Interface) {
            $model = $source['source'];
            $id = null;
            $row = null;
        } else {
            $row = $source['source'];
            if ($row instanceof Zend_Db_Table_Row_Abstract) {
                $model = $row->getTable();
                $primary = current($model->info('primary'));
            } else {
                $model = $row->getModel();
                $primary = $model->getPrimaryKey();
                if (get_class($model) == 'Vps_Model_Db') $model = $model->getTable();
            }
            $id = is_array($primary) ? null : $row->$primary;
            $componentId = isset($row->component_id) ? $row->component_id : null;
        }
        if ($model instanceof Vps_Component_Cache_MetaModel ||
            $model instanceof Vps_Component_Cache_Model ||
            ($model instanceof  Vps_Model_Field && !$primary)
        ) {
            return array();
        }
        if (get_class($model) == 'Vps_Db_Table') return array();
        if ($this->_skipFnF) {
            $m = $model;
            while ($m instanceof Vps_Model_Proxy) { $m = $m->getProxyModel(); }
            if ($m instanceof Vps_Model_FnF) return array();
        }
        $modelname = get_class($model);
        if (!isset($this->_processed[$modelname]) || !in_array($id, $this->_processed[$modelname])) {
            if (!isset($this->_processed[$modelname])) $this->_processed[$modelname] = array();
            $this->_processed[$modelname][] = $id;
            if ($this->_enableProcess) {
                if ($row) {
                    $dirtyColumns = isset($source['dirtyColumns']) ? $source['dirtyColumns'] : null;
                    Vps_Component_Cache::getInstance()->cleanByRow($row, $dirtyColumns);
                } else {
                    // Bei Import kommt ein Model daher
                    Vps_Component_Cache::getInstance()->cleanByModel($model);
                }
            }
            return array($modelname => $id);
        }
        return array();
    }

    public function process()
    {
        $ret = array();

        // View Cache
        if (!Vps_Component_Data_Root::getComponentClass()) return $ret;
        foreach ($this->_process as $action => $process) {
            foreach (array_reverse($process) as $source) {
                foreach ($this->_processCache($source) as $modelname => $id) {
                    if (!isset($ret[$modelname])) $ret[$modelname] = array();
                    $ret[$modelname][] = $id;
                }
            }
        }
        $this->clear();

        // Suchindex
        if (class_exists('Vps_Dao_Index', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
            Vps_Dao_Index::process();
        }

        return $ret;
    }

    // Nur für Tests
    // TODO: Damit sowas nicht notwendig ist, das Ganze testbarer machen (Observer
    // in Row austauschbar, damit man ihn mocken kann und die Klasse hier modularer machen
    public function getProcess()
    {
        return $this->_process;
    }
}
