<?php
class Kwf_Util_Build_Types_Assets extends Kwf_Util_Build_Types_Abstract
{
    private static $_mimeTypeByExtension = array(
        'js' => 'text/javascript',
        'css' => 'text/css',
        'printcss' => 'text/css; media=print'
    );

    public function checkRequirements()
    {
        $uglifyjs = Kwf_Config::getValue('server.uglifyjs');
        exec("$uglifyjs -V 2>&1", $out, $retVal);
        if ($retVal) {
            throw new Kwf_Exception_Client("Can't start uglifyjs, this is required to build javascript assets.\n".
                "use 'npm -g install uglify-js' to install globally or set binary path in config 'server.uglifyjs'");
        }
        $sassc = Kwf_Config::getValue('server.sassc');
        exec("$sassc -h 2>&1", $out, $retVal);
        if ($retVal) {
            throw new Kwf_Exception_Client("Can't start sassc, this is required to build scss assets.\n".
                "install globally or set binary path in config 'server.uglifyjs'");
        }
    }

    private function _buildPackageContents($p, $extension, $language)
    {
        $mimeType = self::$_mimeTypeByExtension[$extension];
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
    }

    private function _buildPackageSourceMap($p, $extension, $language)
    {
        $mimeType = self::$_mimeTypeByExtension[$extension];

        $cacheContents = array(
            'contents' => $p->getPackageContentsSourceMap($mimeType, $language),
            'mimeType' => 'application/json',
            'mtime' => $p->getMaxMTime($mimeType)
        );
        $cacheId = Kwf_Assets_Dispatcher::getCacheIdByPackage($p, $extension.'.map', $language);
        Kwf_Assets_BuildCache::getInstance()->save($cacheContents, $cacheId);
    }

    private function _getAllPackages()
    {
        $packages = array(
            Kwf_Assets_Package_Default::getInstance('Frontend'),
            Kwf_Assets_Package_Default::getInstance('Admin'),
            Kwf_Assets_Package_LazyLoad::getInstance('FrontendDefer', array('Frontend'))
        );
        if (Kwf_Controller_Front::getInstance()->getControllerDirectory('kwf_controller_action_maintenance')) {
            $packages[] = Kwf_Assets_Package_Maintenance::getInstance('Maintenance');
        }
        return $packages;
    }

    private function _getAllLanguages()
    {
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
        $langs = array_unique($langs);
        return $langs;
    }

    public function flagAllPackagesOutdated($extension)
    {
        $langs = $this->_getAllLanguages();
        $packages = $this->_getAllPackages();
        foreach ($packages as $p) {
            foreach ($langs as $language) {
                $cacheId = Kwf_Assets_Dispatcher::getCacheIdByPackage($p, $extension, $language);
                Kwf_Assets_BuildCache::getInstance()->save('outdated', $cacheId);

                $cacheId = Kwf_Assets_Dispatcher::getCacheIdByPackage($p, $extension.'.map', $language);
                Kwf_Assets_BuildCache::getInstance()->save('outdated', $cacheId);
            }
        }
    }

    protected function _build($options)
    {
        if (!file_exists('build/assets')) {
            mkdir('build/assets');
        }


        Kwf_Assets_BuildCache::getInstance()->building = true;
        Kwf_Assets_BuildCache::getInstance()->clean();

        Kwf_Assets_BuildCache::getInstance()->save(time(), 'assetsVersion');

        $langs = $this->_getAllLanguages();
        $packages = $this->_getAllPackages();
        $exts = array('js', 'css', 'printcss');

        echo "\ncalculating dependencies...\n";
        $steps = count($packages) * count($exts);
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        $c->setTextWidth(50);
        $progress = new Zend_ProgressBar($c, 0, $steps);

        $countDependencies = 0;
        foreach ($packages as $p) {
            $depName = $p->getDependencyName();
            foreach ($exts as $extension) {
                $progress->next(1, "$depName $extension");
                $countDependencies += count($p->getFilteredUniqueDependencies(self::$_mimeTypeByExtension[$extension]));

                $cacheId = $p->getMaxMTimeCacheId(self::$_mimeTypeByExtension[$extension]);
                $maxMTime = $p->getMaxMTime(self::$_mimeTypeByExtension[$extension]);
                Kwf_Assets_BuildCache::getInstance()->save($maxMTime, $cacheId);

                //save generated caches for clear-cache-watcher
                $fileName = 'build/assets/package-max-mtime-'.$extension;
                if (!file_exists($fileName) || strpos(file_get_contents($fileName), $cacheId."\n") === false) {
                    file_put_contents($fileName, $cacheId."\n", FILE_APPEND);
                }

                foreach ($langs as $language) {
                    $urls = $p->getPackageUrls(self::$_mimeTypeByExtension[$extension], $language);
                    if (Kwf_Setup::getBaseUrl()) {
                        foreach ($urls as $k=>$i) {
                            $urls[$k] = substr($i, strlen(Kwf_Setup::getBaseUrl()));
                        }
                    }
                    $cacheId = $p->getPackageUrlsCacheId(self::$_mimeTypeByExtension[$extension], $language);
                    Kwf_Assets_BuildCache::getInstance()->save($urls, $cacheId);
                }
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

        foreach ($packages as $p) {
            foreach ($exts as $extension) {
                foreach ($p->getFilteredUniqueDependencies(self::$_mimeTypeByExtension[$extension]) as $dep) {
                    $progress->next(1, "$dep");
                    $dep->warmupCaches();
                }
            }
        }
        $progress->finish();

        echo "generating packages...\n";
        $steps = count($packages) * count($langs) * count($exts) * 2;
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        $c->setTextWidth(50);
        $progress = new Zend_ProgressBar($c, 0, $steps);
        foreach ($packages as $p) {
            $depName = $p->getDependencyName();
            foreach ($langs as $language) {

                foreach ($exts as $extension) {

                    $progress->next(1, "$depName $extension $language");
                    $this->_buildPackageContents($p, $extension, $language);

                    $progress->next(1, "$depName $extension $language map");
                    $this->_buildPackageSourceMap($p, $extension, $language);
                }
            }
        }

        Kwf_Assets_BuildCache::getInstance()->building = false;


        $exts = array('js', 'css');
        foreach ($packages as $p) {
            $depName = $p->getDependencyName();
            $language = $langs[0];
            foreach ($exts as $extension) {
                $cacheId = Kwf_Assets_Dispatcher::getCacheIdByPackage($p, $extension, $language);
                $cacheContents = Kwf_Assets_BuildCache::getInstance()->load($cacheId);
                echo "$depName $extension size: ".Kwf_View_Helper_FileSize::fileSize(strlen(gzencode($cacheContents['contents'], 9, FORCE_GZIP)))."\n";
            }
        }
        $d = Kwf_Assets_Package_Default::getDefaultProviderList()->findDependency('Frontend');
        foreach ($d->getRecursiveDependencies() as $i) {
            if ($i instanceof Kwf_Assets_Dependency_File && $i->getType() == 'ext2') {
                echo "\n[WARNING] Frontend contains ext2\n";
                echo "To improve frontend performance all ext2 dependencies should be moved to FrontendDefer\n\n";
                break;
            }
        }
    }

    public function getTypeName()
    {
        return 'assets';
    }
}
