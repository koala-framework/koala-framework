<?php
class Vps_Form_Field_PoolMulticheckbox extends Vps_Form_Field_MultiCheckbox
{

    public function setPool($pool)
    {
        $model = new Vps_Util_Model_Pool();
        $select = $model->select()
            ->whereEquals('pool', $pool)
            ->whereEquals('visible', 1)
            ->order('pos', 'ASC');
        $values = array();
        foreach ($model->getRows($select) as $r) {
            $values[$r->id] = $r->__toString();
        }
        $this->setValues($values);
        return $this;
    }
}
