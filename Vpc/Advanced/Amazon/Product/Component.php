<?php
class Vpc_Advanced_Amazon_Product_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Amazon.Product');
        $ret['ownModel'] = 'Vps_Component_FieldModel';

        $ret['associateTag'] = 'vps-21';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['product'] = null;
        if ($this->getRow()->asin) {
            $select = new Vps_Model_Select();
            $select->whereEquals('asin', $this->getRow()->asin);
            $select->whereEquals('AssociateTag', $this->_getSetting('associateTag'));
            try {
                $ret['product'] = Vps_Model_Abstract::getInstance('Vps_Util_Model_Amazon_Products')
                    ->getRow($select);
            } catch (Zend_Service_Exception $e) {
            }
        }
        return $ret;
    }

    public function getViewCacheLifetime()
    {
        return 24*60*60;
    }
}
