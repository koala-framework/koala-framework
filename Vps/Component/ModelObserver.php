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
    private $_disabled = 0; // wird zB. beim Import in Proxy ausgeschaltet
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
        $this->_disabled--;
    }

    public function disable()
    {
        $this->_disabled++;
    }

    public function setEnableProcess($enableProcess)
    {
        $this->_enableProcess = $enableProcess;
    }
/*
    public function clear()
    {
        foreach (array_keys($this->_process) as $i) {
            $this->_process[$i] = array();
        }
        $this->_processed = array();
    }
*/

    public function add($function, $source)
    {
        if ($this->_disabled) return;

        if ($source instanceof Vps_Model_Interface) {
            $model = $source;
            $row = null;
        } else {
            $row = $source;
            if ($row instanceof Zend_Db_Table_Row_Abstract) {
                $model = $row->getTable();
                $primary = current($model->info('primary'));
            } else {
                $model = $row->getModel();
                $primary = $model->getPrimaryKey();
                if (get_class($model) == 'Vps_Model_Db') $model = $model->getTable();
            }
        }
        if ($model instanceof Vps_Component_Cache_MetaModel ||
            $model instanceof Vps_Component_Cache_Model ||
            ($model instanceof  Vps_Model_Field && !$primary)
        ) {
            return;
        }
        if (get_class($model) == 'Vps_Db_Table') return;
        if ($this->_skipFnF) {
            $m = $model;
            while ($m instanceof Vps_Model_Proxy) { $m = $m->getProxyModel(); }
            if ($m instanceof Vps_Model_FnF) return;
        }

        $event = null;
        $data = null;
        if ($row) {
            if ($function == 'delete') {
                $event = 'Vps_Component_Event_Row_Deleted';
            } else if ($function == 'update') {
                $event = 'Vps_Component_Event_Row_Updated';
            } else if ($function == 'insert') {
                $event = 'Vps_Component_Event_Row_Inserted';
            }
            if ($event) Vps_Component_Events::fireEvent(new $event($row));
        } else {
            Vps_Component_Events::fireEvent(new Vps_Component_Event_Model_Updated($model));
        }
    }
/*
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
*/
    public function process()
    {
        Vps_Component_Events::fireEvent(new Vps_Component_Event_Row_UpdatesFinished());

        // Suchindex
        if (class_exists('Vps_Dao_Index', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
            Vps_Dao_Index::process();
        }
    }

    // Nur für Tests
    // TODO: Damit sowas nicht notwendig ist, das Ganze testbarer machen (Observer
    // in Row austauschbar, damit man ihn mocken kann und die Klasse hier modularer machen
    public function getProcess()
    {
        return $this->_process;
    }
}
