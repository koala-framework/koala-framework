<?php
class Kwf_Events_ModelObserver
{
    /**
     * @var Kwf_Events_ModelObserver
     */
    static private $_instance;
    private $_skipFnF = true;
    private $_disabled = 0; // wird zB. beim Import in Proxy ausgeschaltet
    private $_modelEventFired = false;

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

    public function add($function, $source, $arg = null)
    {
        if ($this->_disabled) return;

        if ($source instanceof Kwf_Model_Interface) {
            $model = $source;
            $row = null;
        } else {
            $row = $source;
            $model = $row->getModel();
            $primary = $model->getPrimaryKey();
        }
        if (($model instanceof  Kwf_Model_Field && !$primary)) {
            return;
        }
        if ($this->_skipFnF) {
            $m = $model;
            while ($m instanceof Kwf_Model_Proxy) { $m = $m->getProxyModel(); }
            if ($m instanceof Kwf_Model_FnF) return;
        }

        $event = null;
        $data = null;
        if ($row) {
            if ($function == 'delete') {
                $event = 'Kwf_Events_Event_Row_Deleted';
            } else if ($function == 'update') {
                $event = 'Kwf_Events_Event_Row_Updated';
            } else if ($function == 'insert') {
                $event = 'Kwf_Events_Event_Row_Inserted';
            }
            if ($event) Kwf_Events_Dispatcher::fireEvent(new $event($row));
        } else {
            Kwf_Events_Dispatcher::fireEvent(new Kwf_Events_Event_Model_Updated($model, $arg));
        }
        $this->_modelEventFired = true;
    }

    public function process()
    {
        if ($this->_modelEventFired) {
            Kwf_Events_Dispatcher::fireEvent(new Kwf_Events_Event_Row_UpdatesFinished());
        }
    }
}
