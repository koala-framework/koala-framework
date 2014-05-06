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

            foreach ($langs as $language) {

                $exts = array('js', 'css', 'printcss');
                foreach ($exts as $extension) {

                    if ($extension == 'js') $mimeType = 'text/javascript';
                    else if ($extension == 'css') $mimeType = 'text/css';
                    else if ($extension == 'printcss') $mimeType = 'text/css; media=print';
                    $cacheContents = array(
                        'contents' => $p->getPackageContents($mimeType, $language),
                        'mimeType' => $extension == 'js' ? 'text/javascript; charset=utf-8' : 'text/css; charset=utf8',
                        'mtime' => $p->getMaxMTime($mimeType)
                    );

                    $cacheId = Kwf_Assets_Dispatcher::getCacheIdByPackage($p, $extension, $language);
                    Kwf_Assets_BuildCache::getInstance()->save($cacheContents, $cacheId);

                    //save generated caches for clear-cache-watcher
                    $fileName = 'build/assets/output-cache-ids-'.$extension;
                    if (!file_exists($fileName) || strpos(file_get_contents($fileName), $cacheId."\n") === false) {
                        file_put_contents($fileName, $cacheId."\n", FILE_APPEND);
                    }

                    $cacheContents = array(
                        'contents' => $p->getPackageContentsSourceMap($mimeType, $language),
                        'mimeType' => 'application/json',
                        'mtime' => $p->getMaxMTime($mimeType)
                    );
                    $cacheId = Kwf_Assets_Dispatcher::getCacheIdByPackage($p, $extension.'.map', $language);
                    Kwf_Assets_BuildCache::getInstance()->save($cacheContents, $cacheId);
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
