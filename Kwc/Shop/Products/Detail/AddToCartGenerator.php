<?php
class Kwc_Shop_Products_Detail_AddToCartGenerator extends Kwf_Component_Generator_Static
{
    protected function _getChildComponentClass($key, $parentData)
    {
        if ($key != 'product') {
            throw new Kwf_Exception("invalid key '$key'");
        }
        $generators = Kwc_Abstract::getSetting($this->getClass(), 'generators');
        if (count($generators['addToCart']['component']) <= 1) {
            return $generators['addToCart']['component']['product'];
        }
        if ($parentData) {
            foreach ($generators['addToCart']['component'] as $component => $class) {
                if ($component == $parentData->row->component) {
                    return $class;
                }
            }
        }
        return null;
    }
}
