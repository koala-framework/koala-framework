<?php
class Kwc_Root_TrlRoot_Master_Cc_Component extends Kwc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['flags']['hasBaseProperties'] = true;
        $ret['baseProperties'] = array('language');
        return $ret;
    }

    public function getBaseProperty($propertyName)
    {
        if ($propertyName == 'language') {
            return $this->getData()->chained->getLanguage();
        }
        return null;
    }
}
