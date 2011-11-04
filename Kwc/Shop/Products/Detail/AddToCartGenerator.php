<?php
class Kwc_Shop_Products_Detail_AddToCartGenerator extends Kwf_Component_Generator_Static
{
    protected function _getChildComponentClass($key, $parentData)
    {
        $generators = Kwc_Abstract::getSetting($this->getClass(), 'generators');
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
