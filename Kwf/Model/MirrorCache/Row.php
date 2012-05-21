<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_MirrorCache_Row extends Kwf_Model_Proxy_Row
{
    private $_doSyncOnUpdate = false;

    protected function _saveWithoutResetDirty()
    {
        $this->_beforeSave();
        $id = $this->{$this->_getPrimaryKey()};
        if (!$id) {
            $this->_beforeInsert();
        } else {
            $this->_beforeUpdate();
        }
        $this->_beforeSaveSiblingMaster();
        //DEAKTIVIERT: $ret = $this->_row->save();
          //(neue row wird in Model::synchronizeAndInsertRow eingefügt)
        return Kwf_Model_Row_Abstract::_saveWithoutResetDirty(); //nicht parent, der würde wida _row->save machen
        $this->_afterSave();
        if (!$id) {
            $this->_afterInsert();
        } else {
            $this->_afterUpdate();
        }
    }

    protected function _beforeInsert()
    {
        parent::_beforeInsert();

        //access $this->_row-> instead of $this, else we get sibling columns
        $data = array();
        foreach ($this->_row->getDirtyColumns() as $c) {
            $data[$c] = $this->_row->$c;
        }

        $returnedData = $this->getModel()->synchronizeAndInsertRow($data);
        foreach ($returnedData as $k=>$v) {
            //parent aufrufen da die primaryKey exception ignoriert werden soll
            //und doSynOnUpdate nicht benötigt wird
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
                //und doSynOnUpdate nicht benötigt wird
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
        throw new Kwf_Exception_NotYetImplemented("MirrorCacheModel is not able to delete yet");
    }

    public function __set($name, $value)
    {
        if ($name == $this->_getPrimaryKey()) {
            // wenn das implementiert wird vorsicht wegen user-model. bei dem
            // darf der primary key zB wirklich (!) nicht geändert werden
            throw new Kwf_Exception_NotYetImplemented("Primary key may not be changed when using a MirrorCache");
        }

        if (in_array($name, $this->getModel()->getOwnColumns()) && $this->$name != $value) {
            $this->_doSyncOnUpdate = true;
        }

        parent::__set($name, $value);
    }
}
