<?php
class Kwc_Shop_Cart_Update_20150309Legacy00004 extends Kwf_Update
{
    public function update()
    {
        $rows = Kwf_Model_Abstract::getInstance('Kwc_Shop_Cart_OrderProducts')->getRows();
        echo "updading ".count($rows)." rows...\n";
        foreach ($rows as $row) {
            $row->size = (int)$row->size_backup;
            $row->amount = (int)$row->amount_backup;
            $row->save();
        }
    }
}
