<?php
class Kwc_Shop_AddToCartAbstract_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['cart'] = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_Shop_Cart_Component', array('ignoreVisible' => true));
        return $ret;
    }
}
