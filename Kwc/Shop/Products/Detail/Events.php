<?php
class Kwc_Shop_Products_Detail_Events extends Kwc_Directories_Item_Detail_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwc_Shop_ProductPrices',
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onPriceRowUpdate'
        );
        $ret[] = array(
            'class' => 'Kwc_Shop_ProductPrices',
            'event' => 'Kwf_Component_Event_Row_Inserted',
            'callback' => 'onPriceRowUpdate'
        );
        $ret[] = array(
            'class' => 'Kwc_Shop_ProductPrices',
            'event' => 'Kwf_Component_Event_Row_Deleted',
            'callback' => 'onPriceRowUpdate'
        );
        return $ret;
    }

    public function onPriceRowUpdate(Kwf_Component_Event_Row_Abstract $ev)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId('shopProducts_'.$ev->row->getParentRow('Product')->id);
        if ($c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
        }
    }
}
