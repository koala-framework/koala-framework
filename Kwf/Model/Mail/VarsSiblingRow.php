<?php
class Kwf_Model_Mail_VarsSiblingRow extends Kwf_Model_Field_Row
{
    public function _afterInsert()
    {
        parent::_afterInsert();
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
