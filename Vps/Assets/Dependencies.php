<?php
class Vps_Assets_Dependencies
{
    private $_files;
    private $_config;
    private $_assets;
    private $_dependenciesConfig;
    private $_processedDependencies = array();
    private $_processedComponents = array();
    private $_assetsType;
    /**
     * @param string Assets-Typ, Frontend od. Admin
     **/
    public function __construct($assetsType, $config = null)
    {
        if (!$config) {
            $config = Zend_Registry::get('config');
        }
        $this->_config = $config;
        $this->_assetsType = $assetsType;
        if (!isset($this->_config->assets->$assetsType)) {
            throw new Vps_Exception(trlVps("Unknown AssetsType: {0}", $assetsType));
        }
        $this->_assets = $this->_config->assets->$assetsType;
    }

    private function _getFilePath($file)
    {
        return Vps_Assets_Loader::getAssetPath($file, $this->_config->path);
    }

    public function getAssetFiles($fileType = null)
    {
        $ret = array();
        if (!$this->_config->debug->assets->$fileType) {
            $v = $this->_config->application->version;
            $ret[] = "/assets/All{$this->_assetsType}.$fileType?v=$v";
        }
        foreach ($this->getFiles($fileType) as $file) {
            if (substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://') {
                $ret[] = $file;
            } else if ($this->_config->debug->assets->$fileType) {
                $ret[] = '/assets/'.$file;
            }
        }
        return $ret;
    }

    public function getFiles($fileType = null)
    {
        if (!isset($this->_files)) {
            $this->_files = array();
            foreach ($this->_assets as $d=>$v) {
                if ($v) {
                    $this->_processDependency($d);
                }
            }
        }

        if (is_null($fileType)) return $this->_files;

        $files = array();
        foreach ($this->_files as $file) {
            if (substr($file, -strlen($fileType)) == $fileType) {
                if (substr($file, -strlen($fileType)-1) == " $fileType") {
                    //wenn asset hinten mit " js" aufhört das js abschneiden
                    //wird benötigt für googlemaps wo die js-dateien kein js am ende haben
                    $file = substr($file, 0, -strlen($fileType)-1);
                }
                //TODO: wenn sowas öfters gebraucht wird dynamischer machen
                $hostParts = explode('.', $_SERVER['HTTP_HOST']);
                $configDomain = $hostParts[count($hostParts)-2]  // zB 'vivid-planet'
                               .$hostParts[count($hostParts)-1]; // zB 'com'
                if (isset($this->_config->googleMapsApiKeys->$configDomain)) {
                    $file = str_replace(
                        '{$config.googleMapsApiKey}',
                        $this->_config->googleMapsApiKeys->$configDomain,
                        $file
                    );
                }
                $files[] = $file;
            }
        }
        return $files;
    }

    public function getFilePaths($fileType = null)
    {
        $paths = array();
        foreach ($this->getFiles($fileType) as $file) {
            $paths[] = $this->_getFilePath($file);
        }
        return $paths;
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

    public function getPackedAll($fileType)
    {
        return $this->_pack($this->getContentsAll($fileType), $fileType);
    }

    public function getContentsAll($fileType)
    {
        $contents = '';
        foreach ($this->getFiles($fileType) as $file) {
            if (!(substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://')) {
                $contents .= Vps_Assets_Loader::getFileContents($file, $this->_config->path) . "\n";
            }
        }
        return $contents;
    }

    private function _getDependenciesConfig() {
        if (!isset($this->_dependenciesConfig)) {
            $this->_dependenciesConfig = new Zend_Config_Ini(VPS_PATH.'/config.ini', 'dependencies',
                                                array('allowModifications'=>true));
            $this->_dependenciesConfig->merge(new Zend_Config_Ini('application/config.ini', 'dependencies'));
        }
        return $this->_dependenciesConfig;
    }

    private function _processDependency($dependency)
    {
        if (in_array($dependency, $this->_processedDependencies)) return;
        $this->_processedDependencies[] = $dependency;
        if ($dependency == 'Components' || $dependency == 'ComponentsAdmin') {
            foreach ($this->_config->pageClasses as $c) {
                if ($c->class && $c->text) {
                    $this->_processComponentDependency($c->class, $dependency == 'ComponentsAdmin');
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
                $this->_processDependency($d);
            }
        }

        if (isset($deps->files)) {
            foreach ($deps->files as $file) {
                $this->_processDependencyFile($file);
            }
        }
        return;
    }
    private function _processComponentDependency($class, $includeAdminAssets)
    {
        if (in_array($class.$includeAdminAssets, $this->_processedComponents)) return;

        $assets = Vpc_Abstract::getSetting($class, 'assets');
        $assetsAdmin = array();
        if ($includeAdminAssets) {
            $assetsAdmin = Vpc_Abstract::getSetting($class, 'assetsAdmin');
        }
        $this->_processedComponents[] = $class.$includeAdminAssets;
        if (isset($assets['dep'])) {
            foreach ($assets['dep'] as $dep) {
                $this->_processDependency($dep);
            }
        }
        if (isset($assetsAdmin['dep'])) {
            foreach ($assetsAdmin['dep'] as $dep) {
                $this->_processDependency($dep);
            }
        }
        if (isset($assets['files'])) {
            foreach ($assets['files'] as $file) {
                $this->_processDependencyFile($file);
            }
        }
        if (isset($assetsAdmin['files'])) {
            foreach ($assetsAdmin['files'] as $file) {
                $this->_processDependencyFile($file);
            }
        }
        $file = Vpc_Admin::getComponentFile($class, '', 'css');
        if ($file) {
            foreach ($this->_config->path as $type=>$path) {
                if ($path == '.') $path = getcwd();
                if (substr($file, 0, strlen($path)) == $path) {
                    $file = $type.substr($file, strlen($path));
                    if (!in_array($file, $this->_files)) {
                        $this->_files[] = $file;
                        break;
                    }
                }
            }
        }
        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        if (is_array($classes)) {
            foreach ($classes as $class) {
                $this->_processComponentDependency($class, $includeAdminAssets);
            }
        }
    }

    private function _processDependencyFile($file)
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
                    if (!in_array($f, $this->_files)) {
                        $this->_files[] = $f;
                    }
                }
            }
        } else {
            if (!in_array($file, $this->_files)) {
                $this->_files[] = $file;
            }
        }
    }
}
