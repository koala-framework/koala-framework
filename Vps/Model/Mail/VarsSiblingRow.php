<?php
class Vps_Model_Mail_VarsSiblingRow extends Vps_Model_Field_Row
{
    public function save()
    {
        parent::save();
        $additionalStore = $this->getSiblingRow()->getModel()->getAdditionalStore();
        if ($additionalStore) {
            $row = $additionalStore->createRow();
            foreach ($this->toArray() as $k => $v) {
                $row->$k = $v;
            }
            $row->save();
        }
    }
}

