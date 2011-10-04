<?php
interface Vps_Model_Row_Interface
{
    public function __isset($name);
    public function __unset($name);
    public function __get($name);
    public function __set($name, $value);
    public function forceSave(); // speichert in jedem fall, auch wenn sich keine daten geändert haben
    public function save();
    public function delete();
    public function duplicate(array $data = array());
    public function toArray();
    public function isDirty();
    public function getDirtyColumns();

    public function setSiblingRows(array $rows); //internal

    //childRows werden beim speichern autom. mitgespeichert
    public function getChildRows($rule, $select = array());
    public function createChildRow($rule, array $data = array());

    public function getParentRow($rule);
    public function getInternalId();
    public function hasColumn($col);

    //abwärtskompatibilität für Db-Models, sonst null
    public function getTable();
}
