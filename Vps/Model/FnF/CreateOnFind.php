<?php
/**
 * FnF Model das bei find immer einen leeren Datensatz zurückgibt auch wenn
 * gar keiner da ist.
 *
 * wird benötigt für Vpc_Abstract_Composite
 **/
class Vps_Model_FnF_CreateOnFind extends Vps_Model_FnF
{
    public function find($id)
    {
        $ret = parent::find($id);
        if (!$ret->count()) {
            $ret = new $this->_rowsetClass(array(
                'model'     => $this,
                'data'      => array(array()),
                'rowClass'  => $this->_rowClass
            ));
            $ret->current()->{$this->getPrimaryKey()} = $id;
        }
        return $ret;
    }
}
