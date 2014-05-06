<?php
class Kwf_Util_Build_Types_Assets extends Kwf_Util_Build_Types_Abstract
{
    protected function _build($options)
    {
        if (!file_exists('build/assets')) {
            mkdir('build/assets');
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
            foreach(Kwc_Abstract::getComponentClasses() as $c) {
                if (Kwc_Abstract::getFlag($c, 'hasPossibleLanguages')) {
                    foreach (call_user_func(array($c, 'getPossibleLanguages'), $c) as $i) {
                        if (!in_array($i, $langs)) $langs[] = $i;
                    }
                }
            }
        }
        $langs = array_unique($langs);

        Kwf_Assets_BuildCache::getInstance()->building = true;
        Kwf_Assets_BuildCache::getInstance()->clean();

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
                    if (Kwf_Setup::getBaseUrl()) $url = substr($url, strlen(Kwf_Setup::getBaseUrl()));
                    if (substr($url, 0, 21) != '/assets/dependencies/') throw new Kwf_Exception("invalid url: '$url'");
                    $u = substr($url, 21);
                    if (strpos($u, '?') !== false) {
                        $u = substr($u, 0, strpos($u, '?'));
                    }
                    $param = explode('/', $u);
                    $dependencyClass = $param[0];
                    $dependencyParams = $param[1];
                    $language = $param[2];
                    $extension = $param[3];
                    if (is_instance_of($dependencyClass, 'Kwf_Assets_Package')) {
                        Kwf_Assets_Dispatcher::getOutputForUrl($url, Kwf_Media_Output::ENCODING_NONE); //this will fill cache
                    }
                }
            }
        }
        Kwf_Assets_BuildCache::getInstance()->building = false;
    }

    public function getTypeName()
    {
        return 'assets';
    }
}
