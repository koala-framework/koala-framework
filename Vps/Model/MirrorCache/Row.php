<?php
class Vps_Model_MirrorCache_Row extends Vps_Model_Proxy_Row
{
    private $_overrideSetPrimaryException = false;
    protected $_primaryKey;
    private $_doSyncOnUpdate = false;

    protected function _init()
    {
        parent::_init();
        $this->_primaryKey = $this->getModel()->getPrimaryKey();
    }

    /**
     * Wird zB im Usermodel überschrieben, da globale Benutzer nicht neu
     * angelegt werden, sondern die vom Service verwendet werden.
     */
    protected function _getInsertSourceRow()
    {
        return $this->getModel()->getSourceModel()->createRow();
    }

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
        $this->getModel()->synchronize(Vps_Model_MirrorCache::SYNC_ONCE);

        parent::_beforeInsert();
        $sr = $this->_getInsertSourceRow();
        $primaryKey = $this->_primaryKey;
        foreach ($this->_row->toArray() as $k => $v) {
            if ($k != $primaryKey) $sr->$k = $v;
        }
        $sr->save();
        foreach ($sr->toArray() as $k=>$v) {
            if ($k != $primaryKey) $this->$k = $v;
        }
        $this->_overrideSetPrimaryKey($sr->$primaryKey);
    }

    protected function _beforeUpdate()
    {
        if ($this->_doSyncOnUpdate) {
            $this->getModel()->synchronize(Vps_Model_MirrorCache::SYNC_ONCE);
            $this->_doSyncOnUpdate = false;
        }

        parent::_beforeUpdate();

        $sm = $this->getModel()->getSourceModel();
        $primaryKey = $this->_primaryKey;
        $sr = $sm->getRow($this->$primaryKey);
        if (!$sr) {
            throw new Vps_Exception("MirrorCache Datenintegritätsfehler. Bei einem Update konnte die Row im SourceModel nicht gefunden werden.");
        }
        foreach ($this->_row->toArray() as $k => $v) {
            if ($k != $primaryKey) $sr->$k = $v;
        }
        $sr->save();
        foreach ($sr->toArray() as $k=>$v) {
            if ($k != $primaryKey) $this->$k = $v;
        }
        $this->_overrideSetPrimaryKey($sr->$primaryKey);
    }

    /**
     * MirrorCache Model kann noch nicht löschen - bei benutzer könnte es
     * Probleme geben und es wird noch nicht benötigt.
     */
    public function delete()
    {
        throw new Vps_Exception_NotYetImplemented("MirrorCacheModel is not able to delete yet");
    }

    /**
     * Diese Funktion darf nur kontrolliert aufgerufen werden!
     * Wenn der Primary gesetzt werden
     * soll, muss das über __set() laufen und dort implementiert werden.
     * Ist aber bei User-Mirroring zu gefährlich, deshalb wird bei einem set
     * von außen eine Exception geworfen.
     */
    protected function _overrideSetPrimaryKey($primaryValue)
    {
        $this->_overrideSetPrimaryException = true;
        $primaryKey = $this->_primaryKey;
        $this->$primaryKey = $primaryValue;
        $this->_overrideSetPrimaryException = false;
    }

    public function __set($name, $value)
    {
        if ($name == $this->_primaryKey && !$this->_overrideSetPrimaryException) {
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
