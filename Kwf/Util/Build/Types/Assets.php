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

        $mimeTypeByExtension = array(
            'js' => 'text/javascript',
            'css' => 'text/css',
            'printcss' => 'text/css; media=print'
        );


        Kwf_Assets_BuildCache::getInstance()->building = true;
        Kwf_Assets_BuildCache::getInstance()->clean();

        $langs = array_unique($langs);
        $dependencyName = array('Frontend', 'Admin');
        $exts = array('js', 'css', 'printcss');

        echo "\ncalculating dependencies...\n";
        $steps = count($dependencyName) * count($exts);
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        $c->setTextWidth(50);
        $progress = new Zend_ProgressBar($c, 0, $steps);

        $countDependencies = 0;
        foreach ($dependencyName as $depName) {
            $p = Kwf_Assets_Package_Default::getInstance($depName);
            foreach ($exts as $extension) {
                $progress->next(1, "$depName $extension");
                $countDependencies += count($p->getFilteredUniqueDependencies($mimeTypeByExtension[$extension]));
            }
        }
        $progress->finish();

        echo "compiling assets...\n";
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        $c->setTextWidth(50);
        $progress = new Zend_ProgressBar($c, 0, $countDependencies);

        foreach ($dependencyName as $depName) {
            $p = Kwf_Assets_Package_Default::getInstance($depName);
            foreach ($exts as $extension) {
                foreach ($p->getFilteredUniqueDependencies($mimeTypeByExtension[$extension]) as $dep) {
                    $progress->next(1, "$dep");
                    $dep->warmupCaches();
                }
            }
        }
        $progress->finish();

        echo "generating packages...\n";
        $steps = count($dependencyName) * count($langs) * count($exts) * 2;
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        $c->setTextWidth(50);
        $progress = new Zend_ProgressBar($c, 0, $steps);
        foreach ($dependencyName as $depName) {

            foreach ($langs as $language) {

                foreach ($exts as $extension) {

                    $progress->next(1, "$depName $extension $language");

                    $mimeType = $mimeTypeByExtension[$extension];
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

                    $progress->next(1, "$depName $extension $language map");
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
