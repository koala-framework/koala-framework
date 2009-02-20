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
        /* Das clone vor der $row is zwar bisserl eine verarsche, aber da hier
           nur gesammelt und später erst ausgeführt ist,
           wär sonst die row (bzw. dessen Daten) in einer onRowDelete() methode
           einer Admin.php nicht mehr verfügbar
        */
        $this->_process['delete'][] = clone $row;
    }

    public function process()
    {
        $delete = array();
        foreach ($this->_process as $action => $process) {
            foreach ($process as $row) {
                // Cache
                if ($row instanceof Zend_Db_Table_Row_Abstract) {
                    $model = $row->getTable();
                    $primaryKey = current($model->info('primary'));
                } else {
                    $model = $row->getModel();
                    $primaryKey = $model->getPrimaryKey();
                    if ($model instanceof Vps_Model_Db) $model = $model->getTable();
                }
                $id = $row->$primaryKey;
                $delete[get_class($model)][$id] = true;
            }
        }
        foreach ($delete as $model => $val) {
            foreach ($val as $id => $null) {
                Vps_Component_Cache::getInstance()->clean(
                    Vps_Component_Cache::CLEANING_MODE_DEFAULT,
                    array($model, $id, $row)
                );
            }
        }
        Vps_Dao_Index::process();
    }
}
