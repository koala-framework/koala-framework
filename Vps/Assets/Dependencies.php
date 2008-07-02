<?php
class Vps_Assets_Dependencies
{
    private $_files = array();
    private $_config;
    private $_dependenciesConfig;
    private $_processedDependencies = array();
    private $_processedComponents = array();
    /**
     * @param string Assets-Typ, Frontend od. Admin
     **/
    public function __construct()
    {
        $this->_config = Vps_Registry::get('config');
    }

    private function _getFilePath($file)
    {
        return Vps_Assets_Loader::getAssetPath($file, $this->_config->path);
    }

    public function getAssetFiles($assetsType, $fileType = null)
    {
        //$b = Vps_Benchmark::start();
        if ($this->_config->debug->menu) {
            $session = new Zend_Session_Namespace('debug');
            if (isset($session->enable) && $session->enable) {
                $assetsType .= 'Debug';
            }
        }
        $ret = array();
        if (!$this->_config->debug->assets->$fileType || (isset($session->$fileType) && !$session->$fileType)) {
            $v = $this->_config->application->version;
            $ret[] = "/assets/All$assetsType.$fileType?v=$v";
            $allUsed = true;
        }
        foreach ($this->_getFiles($assetsType, $fileType) as $file) {
            if (substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://' || substr($file, 0, 1) == '/') {
                $ret[] = $file;
            } else if (empty($allUsed)) {
                $ret[] = '/assets/'.$file;
            }
        }
        return $ret;
    }

    private function _getFiles($assetsType, $fileType = null)
    {
        if (!isset($this->_files[$assetsType])) {
            $this->_files[$assetsType] = array();
            $assetsSection = $assetsType;
            if (!isset($this->_config->assets->$assetsType)) {
                throw new Vps_Exception("Unknown AssetsType '$assetsType'");
            }
            foreach ($this->_config->assets->$assetsType as $d=>$v) {
                if ($v) {
                    $this->_processDependency($assetsType, $d);
                }
            }
        }

        if (is_null($fileType)) return $this->_files[$assetsType];

        $files = array();
        foreach ($this->_files[$assetsType] as $file) {
            if (substr($file, -strlen($fileType)) == $fileType) {
                if (substr($file, -strlen($fileType)-1) == " $fileType") {
                    //wenn asset hinten mit " js" aufhört das js abschneiden
                    //wird benötigt für googlemaps wo die js-dateien kein js am ende haben
                    $file = substr($file, 0, -strlen($fileType)-1);
                }
                $files[] = $file;
            }
        }

        //hack: übersetzung immer zuletzt anhängen
        if ($fileType == 'js') {
            $files[] = 'vps/Ext/ext-lang-en.js';
        }
        return $files;
    }

    private function _pack($contents, $fileType)
    {
        if ($fileType == 'js') {
            $contents = str_replace("\r", "\n", $contents);

            // remove comments
            $contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);
            $contents = preg_replace('!//[^\n]*!', '', $contents);

            // remove tabs, spaces, newlines, etc. - funktioniert nicht - da fehlen hinundwider ;
            //$contents = str_replace(array("\r", "\n", "\t"), "", $contents);

            // multiple whitespaces
            $contents = str_replace("\t", " ", $contents);
            $contents = preg_replace('/(\n)\n+/', '$1', $contents);
            $contents = preg_replace('/(\n)\ +/', '$1', $contents);
            $contents = preg_replace('/(\ )\ +/', '$1', $contents);

        } else if ($fileType == 'css') {

            $contents = str_replace("\r", "\n", $contents);

            // remove comments
            $contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);

            // multiple whitespaces
            $contents = str_replace("\t", " ", $contents);
            $contents = preg_replace('/(\n)\n+/', '$1', $contents);
            $contents = preg_replace('/(\n)\ +/', '$1', $contents);
            $contents = preg_replace('/(\ )\ +/', '$1', $contents);
        }
        return $contents;
    }

    public function getPackedAll($assetsType, $fileType)
    {
        return $this->_pack($this->getContentsAll($assetsType, $fileType), $fileType);
    }

    public function getContentsAll($assetsType, $fileType)
    {
        if (substr($assetsType, -5) == 'Debug' && !$this->_config->debug->menu) {
            throw new Vps_Exception("Debug Assets are not avaliable as the debug menu is disabled");
        }
        $contents = '';
        foreach ($this->_getFiles($assetsType, $fileType) as $file) {
            if (!(substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://' || substr($file, 0, 1) == '/')) {
                $contents .= Vps_Assets_Loader::getFileContents($file, $this->_config->path) . "\n";
            }
        }
        return $contents;
    }

    private function _getDependenciesConfig()
    {
        if (!isset($this->_dependenciesConfig)) {
            $this->_dependenciesConfig = new Zend_Config_Ini(VPS_PATH.'/config.ini', 'dependencies',
                                                array('allowModifications'=>true));
            $this->_dependenciesConfig->merge(new Zend_Config_Ini('application/config.ini', 'dependencies'));
        }
        return $this->_dependenciesConfig;
    }

    private function _processDependency($assetsType, $dependency)
    {
        if (in_array($assetsType.$dependency, $this->_processedDependencies)) return;
        $this->_processedDependencies[] = $assetsType.$dependency;
        if ($dependency == 'Components' || $dependency == 'ComponentsAdmin') {
            $rootComonent = $this->_config->vpc->rootComponent;
            $this->_processComponentDependency($assetsType, $rootComonent, $dependency == 'ComponentsAdmin');
            foreach ($this->_config->vpc->masterComponents as $c) {
                if ($c) {
                    $this->_processComponentDependency($assetsType, $c, $dependency == 'ComponentsAdmin');
                }
            }
            return;
        }
        if (!isset($this->_getDependenciesConfig()->$dependency)) {
            throw new Vps_Exception("Can't resolve dependency '$dependency'.");
        }
        $deps = $this->_getDependenciesConfig()->$dependency;

        if (isset($deps->dep)) {
            foreach ($deps->dep as $d) {
                $this->_processDependency($assetsType, $d);
            }
        }

        if (isset($deps->files)) {
            foreach ($deps->files as $file) {
                $this->_processDependencyFile($assetsType, $file);
            }
        }
        return;
    }
    private function _processComponentDependency($assetsType, $class, $includeAdminAssets)
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
                $this->_processDependency($assetsType, $dep);
            }
        }
        if (isset($assetsAdmin['dep'])) {
            foreach ($assetsAdmin['dep'] as $dep) {
                $this->_processDependency($assetsType, $dep);
            }
        }
        if (isset($assets['files'])) {
            foreach ($assets['files'] as $file) {
                $this->_processDependencyFile($assetsType, $file);
            }
        }
        if (isset($assetsAdmin['files'])) {
            foreach ($assetsAdmin['files'] as $file) {
                $this->_processDependencyFile($assetsType, $file);
            }
        }

        //alle css-dateien der vererbungshierache includieren
        $componentCssFiles = array();
        $c = $class;

        while ($c) {
            $curClass = $c;
            if (substr($curClass, -10) == '_Component') {
                $curClass = substr($curClass, 0, -10);
            }
            $curClass =  $curClass . '_Component';
            $file = str_replace('_', DIRECTORY_SEPARATOR, $curClass) . '.css';
            foreach ($this->_config->path as $type=>$dir) {
                if ($dir == '.') $dir = getcwd();
                $path = $dir . '/' . $file;
                if (is_file($path)) {
                    $f = $type . '/' . $file;
                    if (!in_array($f, $this->_files[$assetsType])) {
                        $componentCssFiles[] = $f;
                    }
                    break;
                }
            }
            $c = get_parent_class($c);
        }
        //reverse damit css von weiter unten in der vererbungshierachie überschreibt
        $this->_files[$assetsType] = array_merge($this->_files[$assetsType], array_reverse($componentCssFiles));

        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        $classes = array_merge($classes, Vpc_Abstract::getSetting($class, 'plugins'));
        foreach ($classes as $class) {
            if ($class) {
                $this->_processComponentDependency($assetsType, $class, $includeAdminAssets);
            }
        }
    }

    private function _processDependencyFile($assetsType, $file)
    {
        if (substr($file, -2)=="/*") {
            $pathType = substr($file, 0, strpos($file, '/'));
            if (!isset($this->_config->path->$pathType)) {
                throw new Vps_Exception(trlVps("Assets-Path-Type '{0}' not found in config.", $pathType));
            }
            $file = substr($file, strpos($file, '/')); //pathtype abschneiden
            $file = substr($file, 0, -1); //* abschneiden
            $path = $this->_config->path->$pathType.$file;
            if (!file_exists($path)) {
                throw new Vps_Exception(trlVps("Path '{0}' does not exist.", $path));
            }
            $DirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
            foreach ($DirIterator as $file) {
                if (!preg_match('#/\\.svn/#', $file->getPathname())
                    && (substr($file->getPathname(), -3) == '.js'
                        || substr($file->getPathname(), -4) == '.css')) {
                    $f = $file->getPathname();
                    $f = substr($f, strlen($this->_config->path->$pathType));
                    $f = $pathType . $f;
                    if (!in_array($f, $this->_files[$assetsType])) {
                        $this->_files[$assetsType][] = $f;
                    }
                }
            }
        } else {
            if (!in_array($file, $this->_files[$assetsType])) {
                $this->_files[$assetsType][] = $file;
            }
        }
    }
}
