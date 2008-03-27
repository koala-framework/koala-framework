<?php
class Vps_Auto_Field_PoolSelect extends Vps_Auto_Field_Select
{
    public function setPool($pool)
    {
        $table = new Vps_Dao_Pool();
        $this->setValues($table->fetchAll(array('pool = ?' => $pool), 'pos'));
        return $this;
    }
}
