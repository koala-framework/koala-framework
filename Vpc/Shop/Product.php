<?php
class Vpc_Shop_Product extends Vps_Model_Db_Row
{
    public function __toString()
    {
        return $this->title;
    }

    public function delete()
    {
        if ($this->getChildRows('OrderProducts')) {
            throw new Vps_ClientException("Es sind Bestellungen für dieses Produkt vorhanden, Produkt kann nicht gelöscht werden");
        }
        parent::delete();
    }
}
