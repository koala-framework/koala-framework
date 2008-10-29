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
    private $_processed = false;

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function clear()
    {
        foreach (array_keys($this->_process) as $i) {
            $this->_process[$i] = array();
        }
    }


    public function insert($row)
    {
        if ($this->_processed) throw new Vps_Exception("ComponentCache: allready processed");
        $this->_process['insert'][] = $row;
    }

    public function update($row)
    {
        if ($this->_processed) throw new Vps_Exception("ComponentCache: allready processed");
        $this->_process['update'][] = $row;
    }

    public function save($row)
    {
        if ($this->_processed) throw new Vps_Exception("ComponentCache: allready processed");
        $this->_process['save'][] = $row;
    }

    public function delete($row)
    {
        if ($this->_processed) throw new Vps_Exception("ComponentCache: allready processed");
        $this->_process['delete'][] = $row;
    }

    public function process($isLastCall = true)
    {
        $this->_processed = $isLastCall;
        foreach ($this->_process as $action => $process) {
            foreach ($process as $row) {
                foreach (Vpc_Abstract::getComponentClasses() as $c) {
                    $method = 'onRow' . ucfirst($action);
                    if (get_class($row->getModel()) == 'Vps_Model_Db') {
                        Vpc_Admin::getInstance($c)->$method($row->getRow());
                    } else {
                        Vpc_Admin::getInstance($c)->$method($row);
                    }
                }
            }
        }
        Vps_Dao_Index::process();
    }
}
