<?php
/**
 * @ingroup form
 */
class Kwf_Form_Field_PoolSelect extends Kwf_Form_Field_Select
{
    public function setPool($pool)
    {
        $table = new Kwf_Dao_Pool();
        $this->setValues($table->fetchAll(array('pool = ?' => $pool), 'pos'));
        return $this;
    }
}
