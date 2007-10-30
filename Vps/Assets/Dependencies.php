<?php
class Vps_Assets_Dependencies
{
    private $_files;
    private $_config;
    private $_dependenciesConfig;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    private function _getFilePath($file)
    {
        return Vps_Assets_Loader::getAssetPath($file, $this->_config->path);
    }

    public function getAssetFiles($fileType = null)
    {
        $files = $this->getFiles($fileType);
        $ret = array();
        foreach ($files as $file) {
            $ret[] = '/assets/'.$file;
        }
        return $ret;
    }
    public function getFiles($fileType = null)
    {
        if (!isset($this->_files)) {
            $frontendOptions = array(
                'lifetime' => null,
                'automatic_serialization' => true
            );
            $backendOptions = array(
                'cache_dir' => 'application/cache/assets/'
            );
            $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
            
            $checksums = array(
                md5_file(VPS_PATH.'/config.ini'),
                md5_file('application/config.ini')
            );
            if ($cacheContents = $cache->load('dependencies')) {
                if ($cacheContents['checksums'] != $checksums) {
                    $cacheContents = false;
                }
            }

            if(!$cacheContents || true) {
                $this->_files = array();
                $cacheContents = array();
                $cacheContents['checksums'] = $checksums;
                foreach($this->_config->assets as $d=>$v) {
                    if ($v) {
                        $this->_processDependency($d);
                    }
                }
                $cacheContents['files'] = $this->_files;
                $cache->save($cacheContents, 'dependencies');
            } else {
                $this->_files = $cacheContents['files'];
            }
        }

        if (is_null($fileType)) return $this->_files;

        $files = array();
        foreach ($this->_files as $file) {
            if (substr($file, -strlen($fileType)) == $fileType) {
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
        $contents = '';
        foreach($this->getFiles($fileType) as $file) {
            $contents .= $this->_pack($this->getFileContents($file), $fileType) . "\n";
        }
        return $contents;
    }

    public function getFileContents($file)
    {
        $contents = file_get_contents($this->_getFilePath($file));
        if (substr($file, 0, 4)=='ext/') {
            //hack um bei ext-css-dateien korrekte pfade für die bilder zu haben
            $contents = str_replace('../images/', '/assets/ext/resources/images/', $contents);
        }
        return $contents;
    }

    public function getContentsAll($fileType)
    {
        $contents = '';
        foreach($this->getFiles($fileType) as $file) {
            $contents .= $this->getFileContents($file) . "\n";
        }
        return $contents;
    }

    private function _getDependenciesConfig() {
        if(!isset($this->_dependenciesConfig)) {
            $this->_dependenciesConfig = new Zend_Config_Ini(VPS_PATH.'/config.ini', 'dependencies',
                                                array('allowModifications'=>true));
            $this->_dependenciesConfig->merge(new Zend_Config_Ini('application/config.ini', 'dependencies'));
        }
        return $this->_dependenciesConfig;
    }

    protected function _processDependency($dependency)
    {
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
                if (substr($file, -2)=="/*") {
                    $pathType = substr($file, 0, strpos($file, '/'));
                    if (!isset($this->_config->path->$pathType)) {
                        throw new Vps_Exception("Assets-Path-Type '$pathType' not found in config.");
                    }
                    $file = substr($file, strpos($file, '/')); //pathtype abschneiden
                    $file = substr($file, 0, -1); //* abschneiden
                    $path = $this->_config->path->$pathType.$file;
                    if (!file_exists($path)) {
                        throw new Vps_Exception("Path '$path' does not exist.");
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
        return;
    }
}