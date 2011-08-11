<?php
class Vpc_Shop_Product extends Vps_Model_Db_Row
{
    public function __toString()
    {
        return $this->title;
    }

    public function delete()
    {
        foreach ($this->getChildRows('Prices') as $price) {
            if (count($price->getChildRows('OrderProducts')) > 0) {
                throw new Vps_Exception_Client("Es sind Bestellungen für dieses Produkt vorhanden, Produkt kann nicht gelöscht werden");
            }
        }
        parent::delete();
    }
}
