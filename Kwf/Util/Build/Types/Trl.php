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
        $languages = array(Kwf_Registry::get('config')->webCodeLanguage, 'de');
        foreach ($languages as $l) {
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
