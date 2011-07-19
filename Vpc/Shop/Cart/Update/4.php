<?php
class Vpc_Shop_Cart_Update_4 extends Vps_Update
{
    public function update()
    {
        $rows = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_OrderProducts')->getRows();
        echo "updading ".count($rows)." rows...\n";
        foreach ($rows as $row) {
            $row->size = (int)$row->size_backup;
            $row->amount = (int)$row->amount_backup;
            $row->save();
        }
    }
}
