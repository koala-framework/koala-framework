<?php
class Vpc_Advanced_Amazon_Product_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Amazon.Product');
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['product'] = Vps_Model_Abstract::getInstance('Vps_Util_Model_Amazon_Products')
            ->getRow($this->getRow()->asin);
        return $ret;
    }

}
