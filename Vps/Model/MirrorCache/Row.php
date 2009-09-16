<?php
class Vps_Model_MirrorCache_Row extends Vps_Model_Proxy_Row
{
    private $_doSyncOnUpdate = false;

    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->getModel()->getLockTables()) {
            $tableNames = array();
            $models = array($this->getModel());
            $models = array_merge($models, $this->getModel()->getSiblingModels());
            foreach ($models as $m) {
                while ($m instanceof Vps_Model_Proxy) {
                    $m = $m->getProxyModel();
                }
                if ($m instanceof Vps_Model_Db) {
                    $tableNames[] = $m->getTableName();
                }
            }
            if ($tableNames) {
                Vps_Benchmark::count('lock tables');
                $m->executeSql("LOCK TABLES ".implode(" WRITE, ", $tableNames)." WRITE");
            }
        }
    }

    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->getModel()->getLockTables()) {
            $m = $this->getModel();
            while ($m instanceof Vps_Model_Proxy) {
                $m = $m->getProxyModel();
            }
            if ($m instanceof Vps_Model_Db) {
                $m->executeSql("UNLOCK TABLES");
            }
        }
    }

    protected function _beforeInsert()
    {
        parent::_beforeInsert();

        //$this->_row->toArray() statt $this->toArray() da sonst sibling felder zuviel dabei sind
        $data = $this->_row->toArray();

        $returnedData = $this->getModel()->synchronizeAndInsertRow($data);
        foreach ($returnedData as $k=>$v) {
            //parent aufrufen da die primaryKey exception ignoriert werden soll
            //und doSynOnUpdate nicht ben�tigt wird
            parent::__set($k, $v);
        }
    }

    protected function _beforeUpdate()
    {
        parent::_beforeUpdate();

        if ($this->_doSyncOnUpdate) {

            //$this->_row->toArray() statt $this->toArray() da sonst sibling felder zuviel dabei sind
            $data = $this->_row->toArray();

            $returnedData = $this->getModel()->synchronizeAndUpdateRow($data);
            foreach ($returnedData as $k=>$v) {
                //parent aufrufen da die primaryKey exception ignoriert werden soll
                //und doSynOnUpdate nicht ben�tigt wird
                parent::__set($k, $v);
            }

            $this->_doSyncOnUpdate = false;
        }
    }

    /**
     * MirrorCache Model kann noch nicht löschen - bei benutzer könnte es
     * Probleme geben und es wird noch nicht benötigt.
     */
    public function delete()
    {
        throw new Vps_Exception_NotYetImplemented("MirrorCacheModel is not able to delete yet");
    }

    public function __set($name, $value)
    {
        if ($name == $this->_getPrimaryKey()) {
            // wenn das implementiert wird vorsicht wegen user-model. bei dem
            // darf der primary key zB wirklich (!) nicht geändert werden
            throw new Vps_Exception_NotYetImplemented("Primary key may not be changed when using a MirrorCache");
        }

        if (in_array($name, $this->getModel()->getOwnColumns()) && $this->$name != $value) {
            $this->_doSyncOnUpdate = true;
        }

        parent::__set($name, $value);
    }
}
