<?php
class Vpc_Shop_ProductList_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Shop_Products_View_Component';
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['componentName'] = trlVps('Shop.ProductList');
        $ret['componentIcon'] = new Vps_Asset('basket');
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_Shop_Products_Directory_Component');
    }

    public function getSelect($overrideValues = array())
    {
        $ret = parent::getSelect($overrideValues);
        if ($this->getRow()->component) {
            $ret->whereEquals('component', $this->getRow()->component);
        }
        return $ret;
    }
}
