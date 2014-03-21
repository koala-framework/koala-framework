<?php
class Kwf_Util_ClearCache_Types_Assets extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        Kwf_Assets_Cache::getInstance()->clean();
    }

    protected function _refreshCache($options)
    {
        if (!Zend_Registry::get('db')) {
            $this->_output("skipped, no db configured ");
            return;
        }
        $config = Zend_Registry::get('config');

        $langs = array();
        if ($config->webCodeLanguage) $langs[] = $config->webCodeLanguage;

        if ($config->languages) {
            foreach ($config->languages as $lang=>$name) {
                $langs[] = $lang;
            }
        }

        if (Kwf_Component_Data_Root::getComponentClass()) {
            $lngClasses = array();
            foreach(Kwc_Abstract::getComponentClasses() as $c) {
                if (Kwc_Abstract::hasSetting($c, 'baseProperties') &&
                    in_array('language', Kwc_Abstract::getSetting($c, 'baseProperties'))
                ) {
                    $lngClasses[] = $c;
                }
            }
            $lngs = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($lngClasses, array('ignoreVisible'=>true));
            foreach ($lngs as $c) {
                $langs[] = $c->getLanguage();
            }
        }
        $langs = array_unique($langs);

        $dependencyName = array('Frontend', 'Admin');
        foreach ($dependencyName as $depName) {

            $p = Kwf_Assets_Package_Default::getInstance($depName);
            $urls = array();
            foreach ($langs as $language) {
                $urls = array_merge($urls, $p->getPackageUrls('text/javascript', $language));
                $urls = array_merge($urls, $p->getPackageUrls('text/css', $language));
                $urls = array_merge($urls, $p->getPackageUrls('text/css; media=print', $language));
            }
            foreach ($urls as $url) {
                if (substr($url, 0, 1) == '/') {
                    Kwf_Assets_Dispatcher::getOutputForUrl($url, Kwf_Media_Output::ENCODING_NONE); //this will fill cache
                }
            }
        }
    }

    public function getTypeName()
    {
        return 'assets';
    }
    public function doesRefresh() { return true; }
    public function doesClear() { return true; }
}
