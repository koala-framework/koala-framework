<?php
class Kwf_Assets_Dependencies
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
    public function __construct(Kwf_Assets_Loader $loader)
    {
        $this->_loader = $loader;
        $this->_config = $loader->getConfig();
        if ($this->_config) {
            $this->_path = $this->_config->path->toArray();
        } else {
            $this->_path = Kwf_Config::getValueArray('path');
        }
    }

    public function getMaxFileMTime()
    {
        if (isset($this->_cacheMaxFileMTime)) {
            return $this->_cacheMaxFileMTime;
        }
        $cache = Kwf_Assets_Cache::getInstance();
        if (($ret = $cache->load('maxFileMTime')) === false) {
            $ret = 0;
            $assetsType = 'Admin';
            if ($this->_config) {
                $assets = $this->_config->assets->toArray();
            } else {
                $assets = Kwf_Config::getValueArray('assets');
            }
            if (!isset($assets['Admin'])) {
                //für tests wenn keine Admin da, erste aus config nehmen
                $assetsType = key($assets);
            }
            $files = $this->getAssetFiles($assetsType, null, 'web', Kwf_Component_Data_Root::getComponentClass());
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
        Kwf_Benchmark::count('getAssetUrls');
        if ($this->_config) {
            $assets = $this->_config->debug->assets->toArray();
        } else {
            $assets = Kwf_Config::getValueArray('debug.assets');
        }
        $allUsed = false;
        $ret = array();
        if (!$assets[$fileType] || (isset($session->$fileType) && !$session->$fileType)) {
            $v = $this->getMaxFileMTime();
            if (!$language) $language = Kwf_Trl::getInstance()->getTargetLanguage();
            $ret[] = Kwf_Setup::getBaseUrl()."/assets/all/$section/"
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
                        $f = Kwf_Setup::getBaseUrl()."/assets/dynamic/$assetsType/"
                            .($rootComponent?$rootComponent.'/':'')
                            ."$file?v=$v";
                        if ($a->getMTime()) {
                            $f .= "&t=".$a->getMTime();
                        }
                        $ret[] = $f;
                    }
                } else {
                    if (!$allUsed) {
                        $ret[] = Kwf_Setup::getBaseUrl()."/assets/$file";
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
            $cache = Kwf_Assets_Cache::getInstance();
            $this->_files[$assetsType] = $cache->load($cacheId);
            if ($this->_files[$assetsType]===false) {
                Kwf_Benchmark::count('processing dependencies miss', $assetsType);
                $this->_files[$assetsType] = array();
                if ($this->_config) {
                    $allAssets = $this->_config->assets->toArray();
                } else {
                    $allAssets = Kwf_Config::getValueArray('assets');
                }
                if (!isset($allAssets[$assetsType])) {
                    if (strpos($assetsType, ':')) {
                        $configPath = str_replace('_', '/', substr($assetsType, 0, strpos($assetsType, ':')));
                        foreach(explode(PATH_SEPARATOR, get_include_path()) as $dir) {
                            if (file_exists($dir.'/'.$configPath.'/config.ini')) {
                                $sect = 'production';
                                $configFull = new Zend_Config_Ini($dir.'/'.$configPath.'/config.ini', null);
                                if (isset($configFull->{Kwf_Setup::getConfigSection()})) {
                                    $sect = Kwf_Setup::getConfigSection();
                                }
                                $config = clone Kwf_Registry::get('config');
                                $config->merge(new Zend_Config_Ini($dir.'/'.$configPath.'/config.ini', $sect));
                                break;
                            }
                        }
                        if (!isset($config)) {
                            throw new Kwf_Assets_NotFoundException("Unknown AssetsType '$assetsType'");
                        }
                        $assets = $config->assets->{substr($assetsType, strpos($assetsType, ':')+1)};
                    } else {
                        throw new Kwf_Assets_NotFoundException("Unknown AssetsType '$assetsType'");
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
                        throw new Kwf_Exception("Invalid asset file");
                    }
                }

                //store list of generated dependencies caches for clear-cache-watcher
                file_put_contents('cache/assets/generated-dependencies', $cacheId."\n", FILE_APPEND);

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
                    if ((is_string($file) && substr($file, -strlen($fileType)-1) == '.'.$fileType) || substr($file, -strlen($fileType)-2) == '.scss') {
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
        if ($fileType == 'js' && $files) {
            $files[] = 'kwf/Ext/ext-lang-en.js';
        }

        foreach ($files as &$f) {
            if (substr($f, 0, 8) != 'dynamic/' && substr($f, 0, 7) != 'http://' && substr($f, 0, 8) != 'https://') {
                $f = $section . '-' . $f;
            }
        }

        return $files;
    }

    private function _getDependenciesConfig($assetsType)
    {
        $key = 'dep';
        if (strpos($assetsType, ':')) {
            $key = $assetsType;
        }
        if (!isset($this->_dependenciesConfig[$key])) {
            $f = Kwf_Registry::get('config')->assets->dependencies->kwf;
            $ret = new Zend_Config_Ini($f, 'dependencies',
                                                array('allowModifications'=>true));
            foreach (Kwf_Registry::get('config')->assets->dependencies as $k=>$d) {
                if ($k != 'kwf') {
                    $ret->merge(new Zend_Config_Ini($d, 'dependencies'));
                }
            }
            if (strpos($assetsType, ':')) {
                $configPath = str_replace('_', '/', substr($assetsType, 0, strpos($assetsType, ':')));
                foreach(explode(PATH_SEPARATOR, get_include_path()) as $dir) {
                    if (file_exists($dir.'/'.$configPath.'/config.ini')) {
                        $ret->merge(new Zend_Config_Ini($dir.'/'.$configPath.'/config.ini',  'dependencies'));
                        break;
                    }
                }
            }
            $this->_dependenciesConfig[$key] = $ret;
        }
        return $this->_dependenciesConfig[$key];
    }

    private function _processDependency($assetsType, $dependency, $rootComponent)
    {
        if (in_array($assetsType.$dependency, $this->_processedDependencies)) return;
        $this->_processedDependencies[] = $assetsType.$dependency;
        if ($dependency == 'Components' || $dependency == 'ComponentsAdmin') {
            if ($rootComponent) {
                $this->_processComponentDependency($assetsType, $rootComponent, $rootComponent, $dependency == 'ComponentsAdmin');
                if ($dependency == 'Components') {
                    $files = Kwf_Component_Abstract_Admin::getComponentFiles($rootComponent, array(
                        'css' => array('filename'=>'Web', 'ext'=>'css', 'returnClass'=>false, 'multiple'=>true),
                        'printcss' => array('filename'=>'Web', 'ext'=>'printcss', 'returnClass'=>false, 'multiple'=>true),
                        'scss' => array('filename'=>'Web', 'ext'=>'scss', 'returnClass'=>false, 'multiple'=>true),
                    ));
                    foreach ($files as $i) {
                        $this->_addAbsoluteFiles($assetsType, $i);
                    }
                }
            }
            return;
        }
        if (!isset($this->_getDependenciesConfig($assetsType)->$dependency)) {
            throw new Kwf_Exception("Can't resolve dependency '$dependency'");
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


    private function _addAbsoluteFiles($assetsType, $files)
    {
        foreach ($files as $f) {
            if (substr($f, 0, strlen(KWF_PATH)+1) == KWF_PATH.'/') { //first, kwf can be below web
                //kann nur aus kwf
                $f = 'kwf'.substr($f, strlen(KWF_PATH));
            } else if (defined('VKWF_PATH') && substr($f, 0, strlen(VKWF_PATH)+1) == VKWF_PATH.'/') {
                //TODO: this should not be here
                $f = 'vkwf'.substr($f, strlen(VKWF_PATH));
            } else {
                //oder web kommen
                $f = 'web'.substr($f, strlen(getcwd()));
            }
            if (!$this->_hasFile($assetsType, $f)) {
                $this->_files[$assetsType][] = $f;
            }
        }
    }

    private function _processComponentDependency($assetsType, $class, $rootComponent, $includeAdminAssets)
    {
        if (in_array($assetsType.$class.$includeAdminAssets, $this->_processedComponents)) return;

        $assets = Kwc_Abstract::getSetting($class, 'assets');
        $assetsAdmin = array();
        if ($includeAdminAssets) {
            $assetsAdmin = Kwc_Abstract::getSetting($class, 'assetsAdmin');
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
        $files = Kwc_Abstract::getSetting($class, 'componentFiles');
        $componentCssFiles = array();
        foreach (array_merge($files['css'], $files['printcss'], $files['scss'], $files['masterCss'], $files['masterScss']) as $f) {
            $componentCssFiles[] = $f;
        }
        //reverse damit css von weiter unten in der vererbungshierachie überschreibt
        $componentCssFiles = array_reverse($componentCssFiles);

        $this->_addAbsoluteFiles($assetsType, $componentCssFiles);

        $classes = Kwc_Abstract::getChildComponentClasses($class);
        $classes = array_merge($classes, Kwc_Abstract::getSetting($class, 'plugins'));
        foreach (Kwc_Abstract::getSetting($class, 'generators') as $g) {
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
                throw new Kwf_Exception("Assets-Path-Type '$pathType' not found in config.");
            }
            $file = substr($file, strpos($file, '/')); //pathtype abschneiden
            $file = substr($file, 0, -1); //* abschneiden
            $path = $this->_path[$pathType].$file;
            if (!file_exists($path)) {
                throw new Kwf_Exception("Path '$path' does not exist.");
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
            throw new Kwf_Assets_NotFoundException("Assets-Path-Type '$type' for url '$url' not found in config.");
        }
        $p = $this->_path[$type];
        if (!file_exists($p.'/'.$url)) {
            throw new Kwf_Assets_NotFoundException("asset not found $p/$url");
        }
        return $p.'/'.$url;
    }


}
