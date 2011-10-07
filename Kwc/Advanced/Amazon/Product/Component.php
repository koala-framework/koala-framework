<?php
class Kwc_Advanced_Amazon_Product_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Amazon.Product');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';

        $ret['associateTag'] = 'kwf-21';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['product'] = null;
        if ($this->getRow()->asin) {
            $select = new Kwf_Model_Select();
            $select->whereEquals('asin', $this->getRow()->asin);
            $select->whereEquals('AssociateTag', $this->_getSetting('associateTag'));
            try {
                $ret['product'] = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Amazon_Products')
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
