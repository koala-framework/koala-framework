<?php
class Vps_Assets_Dependencies
{
    private $_files = array();
    private $_config;
    private $_path;
    private $_loader;
    private $_dependenciesConfig;
    private $_processedDependencies = array();
    private $_processedComponents = array();
    private $_cacheMaxFileMTime;
    /**
     * @param Zend_Config für tests
     **/
    public function __construct(Vps_Assets_Loader $loader)
    {
        $this->_loader = $loader;
        $this->_config = $loader->getConfig();
        if ($this->_config) {
            $this->_path = $this->_config->path->toArray();
        } else {
            $this->_path = Vps_Config::getValueArray('path');
        }
    }

    public function getMaxFileMTime()
    {
        if (isset($this->_cacheMaxFileMTime)) {
            return $this->_cacheMaxFileMTime;
        }
        $cache = Vps_Assets_Cache::getInstance();
        if (($ret = $cache->load('maxFileMTime')) === false) {
            $ret = 0;
            $assetsType = 'Admin';
            if ($this->_config) {
                $assets = $this->_config->assets->toArray();
            } else {
                $assets = Vps_Config::getValueArray('assets');
            }
            if (!isset($assets['Admin'])) {
                //für tests wenn keine Admin da, erste aus config nehmen
                $assetsType = key($assets);
            }
            $files = $this->getAssetFiles($assetsType, null, 'web', Vps_Component_Data_Root::getComponentClass());
            unset($files['mtime']);
            foreach ($files as $file) {
                if (substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://' || substr($file, 0, 1) == '/') {
                } else if (substr($file, 0, 8) == 'dynamic/') {
                } else {
                    $ret = max($ret, filemtime($this->getAssetPath($file)));
                }
            }
            $ret = array($ret);
            $cache->save($ret, 'maxFileMTime');
        }
        $this->_cacheMaxFileMTime = $ret[0];
        return $ret[0];
    }

    public function getAssetUrls($assetsType, $fileType, $section, $rootComponent, $language = null)
    {
        Vps_Benchmark::count('getAssetUrls');
        if ($this->_config) {
            $menu = $this->_config->debug->menu;
            $assets = $this->_config->debug->assets->toArray();
        } else {
            $menu = Vps_Config::getValue('debug.menu');
            $assets = Vps_Config::getValueArray('debug.assets');
        }
        if ($menu) {
            $session = new Zend_Session_Namespace('debug');
            if (isset($session->enable) && $session->enable) {
                $assetsType .= 'Debug';
            }
        }
        $allUsed = false;
        $ret = array();
        if (!$assets[$fileType] || (isset($session->$fileType) && !$session->$fileType)) {
            $v = $this->getMaxFileMTime();
            if (!$language) $language = Vps_Trl::getInstance()->getTargetLanguage();
            $ret[] = "/assets/all/$section/"
                            .($rootComponent?$rootComponent.'/':'')
                            ."$language/$assetsType.$fileType?v=$v";
            $allUsed = true;
        }

        foreach ($this->getAssetFiles($assetsType, $fileType, $section, $rootComponent) as $file) {
            if (substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://' || substr($file, 0, 1) == '/') {
                $ret[] = $file;
            } else {
                if (substr($file, 0, 8) == 'dynamic/') {
                    $file = substr($file, 8);
                    $arguments = explode(':', $file);
                    $assetClass = array_shift($arguments);
                    $a = new $assetClass($this->_loader, $assetsType, $rootComponent, $arguments);
                    if (!$allUsed || !$a->getIncludeInAll()) {
                        $v = $this->getMaxFileMTime();
                        $f = "/assets/dynamic/$assetsType/"
                            .($rootComponent?$rootComponent.'/':'')
                            ."$file?v=$v";
                        if ($a->getMTime()) {
                            $f .= "&t=".$a->getMTime();
                        }
                        $ret[] = $f;
                    }
                } else {
                    if (!$allUsed) {
                        $ret[] = "/assets/$file";
                    }
                }
            }
        }
        return $ret;
    }

    public function getAssetFiles($assetsType, $fileType, $section, $rootComponent)
    {
        if (!isset($this->_files[$assetsType])) {
            $cacheId = 'dependencies'.str_replace(':', '_', $assetsType).$rootComponent;
            $cache = Vps_Assets_Cache::getInstance();
            $this->_files[$assetsType] = $cache->load($cacheId);
            if ($this->_files[$assetsType]===false) {
                Vps_Benchmark::count('processing dependencies miss', $assetsType);
                $this->_files[$assetsType] = array();
                if ($this->_config) {
                    $allAssets = $this->_config->assets->toArray();
                } else {
                    $allAssets = Vps_Config::getValueArray('assets');
                }
                if (!isset($allAssets[$assetsType])) {
                    if (strpos($assetsType, ':')) {
                        $configPath = str_replace('_', '/', substr($assetsType, 0, strpos($assetsType, ':')));
                        foreach(explode(PATH_SEPARATOR, get_include_path()) as $dir) {
                            if (file_exists($dir.'/'.$configPath.'/config.ini')) {
                                $sect = 'vivid';
                                $configFull = new Zend_Config_Ini($dir.'/'.$configPath.'/config.ini', null);
                                if (isset($configFull->{Vps_Setup::getConfigSection()})) {
                                    $sect = Vps_Setup::getConfigSection();
                                }
                                $config = clone Vps_Registry::get('config');
                                $config->merge(new Zend_Config_Ini($dir.'/'.$configPath.'/config.ini', $sect));
                                break;
                            }
                        }
                        if (!isset($config)) {
                            throw new Vps_Assets_NotFoundException("Unknown AssetsType '$assetsType'");
                        }
                        $assets = $config->assets->{substr($assetsType, strpos($assetsType, ':')+1)};
                    } else {
                        throw new Vps_Assets_NotFoundException("Unknown AssetsType '$assetsType'");
                    }
                } else {
                    $assets = $allAssets[$assetsType];
                }
                foreach ($assets as $d=>$v) {
                    if ($v) {
                        $this->_processDependency($assetsType, $d, $rootComponent);
                    }
                }

                //zur sicherheit überprüfen ob eh keine dynamischen assets cached werden
                foreach ($this->_files[$assetsType] as $f) {
                    if (!is_string($f)) {
                        throw new Vps_Exception("Invalid asset file");
                    }
                }
                $cache->save($this->_files[$assetsType], $cacheId);
            }
        }

        if (is_null($fileType)) {
            $files = $this->_files[$assetsType];
        } else {
            $files = array();
            foreach ($this->_files[$assetsType] as $file) {
                if (substr($file, 0, 8) == 'dynamic/') {
                    $arguments = explode(':', substr($file, 8));
                    $f = array_shift($arguments);
                    $f = new $f($this->_loader, $assetsType, $rootComponent, $arguments);
                    if ($f->getType() == $fileType) {
                        $files[] = $file;
                    }
                } else {
                    if ((is_string($file) && substr($file, -strlen($fileType)-1) == '.'.$fileType)) {
                        if (is_string($file) && substr($file, -strlen($fileType)-1) == " $fileType") {
                            //wenn asset hinten mit " js" aufhört das js abschneiden
                            //wird benötigt für googlemaps wo die js-dateien kein js am ende haben
                            $file = substr($file, 0, -strlen($fileType)-1);
                        }
                        $files[] = $file;
                    }
                }
            }
        }
        //hack: übersetzung immer zuletzt anhängen
        if ($fileType == 'js') {
            $files[] = 'vps/Ext/ext-lang-en.js';
        }

        foreach ($files as &$f) {
            if (substr($f, 0, 8) != 'dynamic/') {
                $f = $section . '-' . $f;
            }
        }
        return $files;
    }

    private function _getDependenciesConfig($assetsType)
    {
        if (!isset($this->_dependenciesConfig[$assetsType])) {
            $ret = new Zend_Config_Ini(VPS_PATH.'/config.ini', 'dependencies',
                                                array('allowModifications'=>true));
            $ret->merge(new Zend_Config_Ini('application/config.ini', 'dependencies'));
            if (strpos($assetsType, ':')) {
                $configPath = str_replace('_', '/', substr($assetsType, 0, strpos($assetsType, ':')));
                foreach(explode(PATH_SEPARATOR, get_include_path()) as $dir) {
                    if (file_exists($dir.'/'.$configPath.'/config.ini')) {
                        $ret->merge(new Zend_Config_Ini($dir.'/'.$configPath.'/config.ini',  'dependencies'));
                        break;
                    }
                }
            }
            $this->_dependenciesConfig[$assetsType] = $ret;
        }
        return $this->_dependenciesConfig[$assetsType];
    }

    private function _processDependency($assetsType, $dependency, $rootComponent)
    {
        if (in_array($assetsType.$dependency, $this->_processedDependencies)) return;
        $this->_processedDependencies[] = $assetsType.$dependency;
        if ($dependency == 'Components' || $dependency == 'ComponentsAdmin') {
            if ($rootComponent) {
                $this->_processComponentDependency($assetsType, $rootComponent, $rootComponent, $dependency == 'ComponentsAdmin');
            }
            return;
        }
        if (!isset($this->_getDependenciesConfig($assetsType)->$dependency)) {
            throw new Vps_Exception("Can't resolve dependency '$dependency'");
        }
        $deps = $this->_getDependenciesConfig($assetsType)->$dependency;

        if (isset($deps->dep)) {
            foreach ($deps->dep as $d) {
                $this->_processDependency($assetsType, $d, $rootComponent);
            }
        }

        if (isset($deps->files)) {
            foreach ($deps->files as $file) {
                $this->_processDependencyFile($assetsType, $file, $rootComponent);
            }
        }
        return;
    }

    private function _hasFile($assetsType, $file)
    {
        return in_array($file, $this->_files[$assetsType], true);
        /*
        //in_array scheint mit php 5.1 mit objekten nicht zu funktionieren
        foreach ($this->_files[$assetsType] as $f) {
            if (gettype($f) == gettype($file) && $f == $file) {
                return true;
            }
        }
        return false;
        */
    }

    private function _processComponentDependency($assetsType, $class, $rootComponent, $includeAdminAssets)
    {
        if (in_array($assetsType.$class.$includeAdminAssets, $this->_processedComponents)) return;

        $assets = Vpc_Abstract::getSetting($class, 'assets');
        $assetsAdmin = array();
        if ($includeAdminAssets) {
            $assetsAdmin = Vpc_Abstract::getSetting($class, 'assetsAdmin');
        }
        $this->_processedComponents[] = $assetsType.$class.$includeAdminAssets;
        if (isset($assets['dep'])) {
            foreach ($assets['dep'] as $dep) {
                $this->_processDependency($assetsType, $dep, $rootComponent);
            }
        }
        if (isset($assetsAdmin['dep'])) {
            foreach ($assetsAdmin['dep'] as $dep) {
                $this->_processDependency($assetsType, $dep, $rootComponent);
            }
        }
        if (isset($assets['files'])) {
            foreach ($assets['files'] as $file) {
                $this->_processDependencyFile($assetsType, $file, $rootComponent);
            }
        }
        if (isset($assetsAdmin['files'])) {
            foreach ($assetsAdmin['files'] as $file) {
                $this->_processDependencyFile($assetsType, $file, $rootComponent);
            }
        }

        //alle css-dateien der vererbungshierache includieren
        $files = Vpc_Abstract::getSetting($class, 'componentFiles');
        $componentCssFiles = array();
        foreach (array_merge($files['css'], $files['printcss']) as $f) {
            if (substr($f, 0, strlen(VPS_PATH)) == VPS_PATH) { //zuerst, da vps in web liegen kann
                //kann nur aus vps
                $f = 'vps'.substr($f, strlen(VPS_PATH));
            } else {
                //oder web kommen
                $f = 'web'.substr($f, strlen(getcwd()));
            }
            if (!$this->_hasFile($assetsType, $f)) {
                $componentCssFiles[] = $f;
            }
        }

        //reverse damit css von weiter unten in der vererbungshierachie überschreibt
        $this->_files[$assetsType] = array_merge($this->_files[$assetsType], array_reverse($componentCssFiles));

        $classes = Vpc_Abstract::getChildComponentClasses($class);
        $classes = array_merge($classes, Vpc_Abstract::getSetting($class, 'plugins'));
        foreach (Vpc_Abstract::getSetting($class, 'generators') as $g) {
            if (isset($g['plugins'])) {
                $classes = array_merge($classes, $g['plugins']);
            }
        }

        foreach ($classes as $class) {
            if ($class) {
                $this->_processComponentDependency($assetsType, $class, $rootComponent, $includeAdminAssets);
            }
        }
    }

    private function _processDependencyFile($assetsType, $file)
    {
        if (is_string($file) && substr($file, -2)=="/*") {
            $pathType = substr($file, 0, strpos($file, '/'));
            if (!isset($this->_path[$pathType])) {
                throw new Vps_Exception("Assets-Path-Type '$pathType' not found in config.");
            }
            $file = substr($file, strpos($file, '/')); //pathtype abschneiden
            $file = substr($file, 0, -1); //* abschneiden
            $path = $this->_path[$pathType].$file;
            if (!file_exists($path)) {
                throw new Vps_Exception("Path '$path' does not exist.");
            }
            $DirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
            foreach ($DirIterator as $file) {
                if (!preg_match('#/\\.svn/#', $file->getPathname())
                    && (substr($file->getPathname(), -3) == '.js'
                        || substr($file->getPathname(), -4) == '.css')) {
                    $f = $file->getPathname();
                    $f = substr($f, strlen($this->_path[$pathType]));
                    $f = $pathType . $f;
                    if (!$this->_hasFile($assetsType, $f)) {
                        $this->_files[$assetsType][] = $f;
                    }
                }
            }
        } else if ($file) {
            if (!$this->_hasFile($assetsType, $file)) {
                $this->_files[$assetsType][] = $file;
            }
        }
    }
    public function getAssetPath($url)
    {
        if (file_exists($url)) return $url;

        $type = substr($url, 0, strpos($url, '/'));
        $url = substr($url, strpos($url, '/')+1);
        if (substr($type, 0, 3) == 'fx_') {
            $type = substr($url, 0, strpos($url, '/'));
            $url = substr($url, strpos($url, '/')+1);
        }
        if (strpos($type, '-')!==false) {
            $type = substr($type, strpos($type, '-')+1); //section abschneiden
        }
        if (!isset($this->_path[$type])) {
            throw new Vps_Assets_NotFoundException("Assets-Path-Type '$type' for url '$url' not found in config.");
        }
        $p = $this->_path[$type];
        if (!file_exists($p.'/'.$url)) {
            throw new Vps_Assets_NotFoundException("Assets '$p/$url' not found");
        }
        return $p.'/'.$url;
    }


}
