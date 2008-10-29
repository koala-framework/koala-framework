<?php
class Vps_Trl_Model_Abstract extends Vps_Model_ProxyCache
{
    protected function _getTargetLanguages ()
    {
        $config = Zend_Registry::get('config');
        if ($config->languages) {
            $languages = array_values($config->languages->toArray());
        } else if ($config->webCodeLanguage) {
            $languages = array($config->webCodeLanguage);
        }
        if (empty($languages)) {
            throw new Vps_Exception('Neither config languages nor config webCodeLanguage set.');
        }
        $ret = array();
        foreach ($languages as $language) {
            $ret[] = $language;
            $ret[] = $language.'_plural';
        }
        return $ret;
    }

    protected function _getWebCodeLanguage()
    {
        $config = Zend_Registry::get('config');
        if ($config->webCodeLanguage) {
            return $config->webCodeLanguage;
        } else {
            throw new Vps_Exception('No webcodelanguage is set!');
        }
    }


}