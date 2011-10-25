<?php
class Kwc_Shop_Cart_Checkout_Form_Success_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $row = $this->getData()->parent->getComponent()->getFormRow();
        $ret['payment'] = $this->getData()->parent->parent
                                ->getChildComponent('-'.$row->payment);
        return $ret;
    }
}
