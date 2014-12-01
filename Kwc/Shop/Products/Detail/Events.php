<?php
class Kwc_Shop_Products_Detail_Events extends Kwc_Directories_Item_Detail_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwc_Shop_ProductPrices',
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onPriceRowUpdate'
        );
        $ret[] = array(
            'class' => 'Kwc_Shop_ProductPrices',
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onPriceRowUpdate'
        );
        $ret[] = array(
            'class' => 'Kwc_Shop_ProductPrices',
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onPriceRowUpdate'
        );
        return $ret;
    }

    public function onPriceRowUpdate(Kwf_Events_Event_Row_Abstract $ev)
    {
        $components = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
            'shopProducts_'.$ev->row->getParentRow('Product')->id, array('ignoreVisible' => true)
        );
        foreach ($components as $component) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $component));
        }
    }
}
