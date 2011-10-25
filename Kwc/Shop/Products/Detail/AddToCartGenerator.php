<?php
class Kwc_Shop_Products_Detail_AddToCartGenerator extends Kwf_Component_Generator_Static
{
    protected function _getChildComponentClasses($parentData = null)
    {
        $generators = Kwc_Abstract::getSetting($this->getClass(), 'generators');
        if ($parentData) {
            foreach ($generators['addToCart']['component'] as $component => $class) {
                if ($component == $parentData->row->component) {
                    return array('addToCart' => $class);
                }
            }
        }
        return array();
    }

}
