<?php
class Vps_Model_Mail_VarsSiblingRow extends Vps_Model_Field_Row
{
    /**
     * Nötig, da das save u.U. zweimal aufgetrufen wird, aber wir die additional
     * row nur einmal speichern möchten.
     */
    private $_additionalRowCreated = false;

    public function save()
    {
        parent::save();
        $additionalStore = $this->getSiblingRow()->getModel()->getAdditionalStore();
        if ($additionalStore && !$this->_additionalRowCreated) {
            $row = $additionalStore->createRow();
            foreach ($this->toArray() as $k => $v) {
                $row->$k = $v;
            }
            $row->save();
            $this->_additionalRowCreated = true;
        }
    }
}

