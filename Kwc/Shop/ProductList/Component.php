<?php
class Kwc_Shop_ProductList_Component extends Kwc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Kwc_Shop_Products_View_Component';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['componentName'] = trlKwf('Shop.ProductList');
        $ret['componentIcon'] = new Kwf_Asset('basket');
        return $ret;
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        return self::_getParentItemDirectoryClasses($directoryClass, 4);
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        $ret = array();
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByClass('Kwc_Shop_Products_Directory_Component') as $component) {
            $ret[] = $component->componentClass;
        }
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_Shop_Products_Directory_Component');
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
