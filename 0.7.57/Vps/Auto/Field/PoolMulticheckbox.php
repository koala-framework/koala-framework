<?php
class Vps_Auto_Field_PoolMulticheckbox extends Vps_Auto_Field_MultiCheckbox
{

    public function setPool($pool)
    {
        $table = new Vps_Dao_Pool();
        $where = array('pool = ?' => $pool);
        $this->setValues($table->fetchAll($where));
        return $this;
    }
}
