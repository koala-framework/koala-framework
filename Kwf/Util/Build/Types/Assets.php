<?php
class Kwf_Util_Build_Types_Assets extends Kwf_Util_Build_Types_Abstract
{
    private static $_mimeTypeByExtension = array(
        'js' => 'text/javascript',
        'defer.js' => 'text/javascript; defer',
        'css' => 'text/css',
        'ie8.css' => 'text/css; ie8',
    );

    private function _buildPackageContents($cacheContents, $p, $extension, $language)
    {
        $cacheId = Kwf_Assets_Dispatcher::getInstance()->getCacheIdByPackage($p, $extension, $language);
        Kwf_Assets_BuildCache::getInstance()->save($cacheContents, $cacheId);

        //save generated caches for clear-cache-watcher
        $fileName = 'build/assets/output-cache-ids-'.$extension;
        if (!file_exists($fileName) || strpos(file_get_contents($fileName), $cacheId."\n") === false) {
            file_put_contents($fileName, $cacheId."\n", FILE_APPEND);
        }
    }

    private function _buildPackageSourceMap($cacheContents, $p, $extension, $language)
    {
        $cacheId = Kwf_Assets_Dispatcher::getInstance()->getCacheIdByPackage($p, $extension.'.map', $language);
        Kwf_Assets_BuildCache::getInstance()->save($cacheContents, $cacheId);
    }

    public function getAllPackages()
    {
        $packages = array();
        foreach (Kwf_Config::getValueArray('assets.packageFactories') as $i) {
            if (!$i) continue;
            if (!is_instance_of($i, 'Kwf_Assets_Package_FactoryInterface')) {
                throw new Kwf_Exception("'$i' doesn't implement Kwf_Assets_Package_FactoryInterface");
            }
            $packages = array_merge($packages, call_user_func(array($i, 'createPackages')));
        }
        return $packages;
    }

    public function getAllLanguages()
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
            foreach (Kwc_Abstract::getComponentClasses() as $c) {
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
        $langs = $this->getAllLanguages();
        $packages = $this->getAllPackages();
        foreach ($packages as $p) {
            foreach ($langs as $language) {
                $cacheId = Kwf_Assets_Dispatcher::getInstance()->getCacheIdByPackage($p, $extension, $language);
                if (Kwf_Assets_BuildCache::getInstance()->load($cacheId) !== false) {
                    Kwf_Assets_BuildCache::getInstance()->save('outdated', $cacheId);
                }

                $cacheId = Kwf_Assets_Dispatcher::getInstance()->getCacheIdByPackage($p, $extension.'.map', $language);
                if (Kwf_Assets_BuildCache::getInstance()->load($cacheId) !== false) {
                    Kwf_Assets_BuildCache::getInstance()->save('outdated', $cacheId);
                }
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

        $langs = $this->getAllLanguages();
        $packages = $this->getAllPackages();
        $exts = array('js', 'defer.js', 'css', 'ie8.css');

        $providers = array();
        foreach ($packages as $p) {
            foreach ($p->getProviderList()->getProviders() as $provider) {
                if (!in_array($provider, $providers)) {
                    $providers[] = $provider;
                }
            }
        }
        echo "\ninitializing providers...\n";
        $steps = count($providers);
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        $c->setTextWidth(80);
        $progress = new Zend_ProgressBar($c, 0, $steps);
        foreach ($providers as $provider) {
            $progress->next(1, get_class($provider));
            $provider->initialize();
        }
        $progress->finish();

        echo "calculating dependencies...\n";
        $steps = count($packages) * count($exts);
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        $c->setTextWidth(80);
        $progress = new Zend_ProgressBar($c, 0, $steps);

        $countDependencies = 0;
        foreach ($packages as $p) {
            $depName = $p->getDependencyName();
            foreach ($exts as $extension) {
                $progress->next(1, "$depName $extension");
                $p->getFilteredUniqueDependencies(self::$_mimeTypeByExtension[$extension]);
            }
            $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_Iterator_UniqueFilter(new Kwf_Assets_Dependency_Iterator_Recursive($p->getDependency(), Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL)), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($it as $i) {
                $countDependencies++;
            }
        }
        $progress->finish();

        echo "compiling assets...\n";
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_ETA,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        $c->setTextWidth(80);
        $progress = new Zend_ProgressBar($c, 0, $countDependencies);

        foreach ($packages as $p) {
            $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_Iterator_UniqueFilter(new Kwf_Assets_Dependency_Iterator_Recursive($p->getDependency(), Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL)), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($it as $dep) {
                $progress->next(1, "$dep");
                if ($dep->getMimeType()) {
                    $mimeType = $dep->getMimeType();
                    $p->warmupDependencyCaches($dep, $mimeType, $progress);
                    if ($mimeType == 'text/css') {
                        $p->warmupDependencyCaches($dep, 'text/css; ie8', $progress);
                    }
                }
            }
        }
        $progress->finish();

        echo "generating packages...\n";
        $steps = count($packages) * count($langs) * count($exts) * 3;
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        $c->setTextWidth(80);
        $progress = new Zend_ProgressBar($c, 0, $steps);
        foreach ($packages as $p) {
            $depName = $p->getDependencyName();
            foreach ($langs as $language) {

                foreach ($exts as $extension) {

                    $progress->next(1, "$depName $extension $language url");
                    $urls = $p->getPackageUrls(self::$_mimeTypeByExtension[$extension], $language);
                    if (Kwf_Setup::getBaseUrl()) {
                        foreach ($urls as $k=>$i) {
                            $urls[$k] = substr($i, strlen(Kwf_Setup::getBaseUrl()));
                        }
                    }
                    $cacheId = $p->getPackageUrlsCacheId(self::$_mimeTypeByExtension[$extension], $language);
                    Kwf_Assets_BuildCache::getInstance()->save($urls, $cacheId);

                    foreach ($urls as $urlNum=>$url) {
                        $param = explode('/', $url);
                        $urlLanguage = $param[5];
                        $urlExtension = $param[6];
                        $urlExtension = substr($urlExtension, 0, strpos($urlExtension, '?'));
                        $contents = $p->getUrlContents($urlExtension, $urlLanguage);

                        $progressIncrement = $urlNum == 0 ? 1 : 0;
                        $progress->next($progressIncrement, "$depName $urlExtension $urlLanguage source");
                        $this->_buildPackageContents($contents, $p, $urlExtension, $urlLanguage);

                        $progress->next($progressIncrement, "$depName $urlExtension $urlLanguage map");
                        $this->_buildPackageSourceMap($contents, $p, $urlExtension, $urlLanguage);
                    }

                    if (!$urls) {
                        //no urls, just increment progress
                        $progress->next(2);
                    }
                }

            }
        }

        Kwf_Assets_Cache::getInstance()->clean();
        Kwf_Assets_BuildCache::getInstance()->building = false;

    }

    public function getTypeName()
    {
        return 'assets';
    }
}
