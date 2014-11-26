<?php
class Kwf_Util_Build_Types_Trl extends Kwf_Util_Build_Types_Abstract
{
    protected function _build()
    {
        if (!file_exists('build/trl')) {
            mkdir('build/trl');
        }

        foreach (glob('build/trl/*') as $f) {
            unlink($f);
        }
        Kwf_Trl::getInstance()->clearCache();

        $config = Zend_Registry::get('config');
        $langs = array();
        if ($config->webCodeLanguage) $langs[] = $config->webCodeLanguage;

        if ($config->languages) {
            foreach ($config->languages as $lang=>$name) {
                $langs[] = $lang;
            }
        }
        if (Kwf_Component_Data_Root::getComponentClass()) {
            foreach(Kwc_Abstract::getComponentClasses() as $c) {
                if (Kwc_Abstract::getFlag($c, 'hasAvailableLanguages')) {
                    foreach (call_user_func(array($c, 'getAvailableLanguages'), $c) as $i) {
                        if (!in_array($i, $langs)) $langs[] = $i;
                    }
                }
            }
        }

        foreach ($langs as $l) {
            $cacheFileName = null;
            $c = Kwf_Trl::getInstance()->_loadCache(Kwf_Trl::SOURCE_WEB, $l, true, $cacheFileName);
            if ($cacheFileName) file_put_contents($cacheFileName, serialize($c));

            $cacheFileName = null;
            $c = Kwf_Trl::getInstance()->_loadCache(Kwf_Trl::SOURCE_WEB, $l, false, $cacheFileName);
            if ($cacheFileName) file_put_contents($cacheFileName, serialize($c));

            $cacheFileName = null;
            $c = Kwf_Trl::getInstance()->_loadCache(Kwf_Trl::SOURCE_KWF, $l, true, $cacheFileName);
            if ($cacheFileName) file_put_contents($cacheFileName, serialize($c));

            $cacheFileName = null;
            $c = Kwf_Trl::getInstance()->_loadCache(Kwf_Trl::SOURCE_KWF, $l, false, $cacheFileName);
            if ($cacheFileName) file_put_contents($cacheFileName, serialize($c));
        }
    }

    public function getTypeName()
    {
        return 'trl';
    }
}
