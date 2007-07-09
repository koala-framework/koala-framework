<?php
class Vps_Assets_Dependencies
{
    private $_files = array();
    private $_paths;

    //löst pfade wie "asset.vps/images/" auf
    public static function resolveAssetPaths($paths)
    {
        foreach($paths as $k=>$p) {
            if (substr($p, 0, 6)=='asset.') {
                $p = substr($p, 6);
                $i = substr($p, 0, strpos($p, '/'));
                if (!isset($paths[$i])) {
                    throw new Vps_Exception("Can't resolve asset-path: 'asset.$i' is unknown.");
                }
                $p = substr($p, strpos($p, '/')+1);
                $paths[$k] = $paths[$i].$p;
            }
        }
        return $paths;
    }

    public function __construct($paths, $configFile, $configSection)
    {
        if (!is_array($paths)) {
            $paths = $paths->toArray();
        }
        $this->_paths = self::resolveAssetPaths($paths);

        $frontendOptions = array(
            'lifetime' => null,
            'automatic_serialization' => true
        );
        $backendOptions = array(
            'cache_dir' => 'application/cache/assets/'
        );
        require_once 'Zend/Cache.php';
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        
        $checksums = array(
            md5_file(VPS_PATH.'/Vps_js/dependencies.ini'),
            md5_file($configFile)
        );
        
        if ($cacheContents = $cache->load('dependencies')) {
            if ($cacheContents['checksums'] != $checksums
                || $cacheContents['configSection'] != $configSection
                || $cacheContents['paths'] != $paths) {
                $cacheContents = false;
            }
        }

        if(!$cacheContents) {
            $cacheContents = array();
            $cacheContents['checksums'] = $checksums;
            $cacheContents['configSection'] = $configSection;
            $cacheContents['paths'] = $paths;
            $dependencies = new Zend_Config_Ini($configFile, $configSection);
            foreach($dependencies as $d) {
                $this->_processDependency($d);
            }
            $cacheContents['files'] = $this->_files;
            $cache->save($cacheContents, 'dependencies');
        } else {
            $this->_files = $cacheContents['files'];
        }
    }

    private function _getFilePath($file)
    {
        $pathType = substr($file, 0, strpos($file, '/'));
        if (!isset($this->_paths[$pathType])) {
            require_once 'Vps/Exception.php';
            throw new Vps_Exception("JS-Path-Type '$pathType' not found in config.");
        }
        $path = $this->_paths[$pathType].substr($file, strpos($file, '/'));
        if(!file_exists($path)) {
            require_once 'Vps/Exception.php';
            throw new Vps_Exception("JS-File '$path' does not exist.");
        }
        return $path;
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
            require_once 'Vps/Assets/JavaScriptPacker.php';
            $packer = new Vps_Assets_JavaScriptPacker($contents, 'Normal', true, false);
            return $packer->pack();
        } else {
            return $contents;
        }
    }

    public function getPackedAll($fileType)
    {
        $frontendOptions = array(
            'lifetime' => null
        );
        $backendOptions = array(
            'cache_dir' => 'application/cache/assets/'
        );
        require_once 'Zend/Cache.php';
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

        if (!$contents = $cache->load($fileType.'AllPacked')) {
            $contents = $this->_pack($this->getContentsAll($fileType), $fileType);
            $cache->save($contents, $fileType.'AllPacked');
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
        foreach($this->_files as $file) {
            if (substr($file, -strlen($fileType)) == $fileType) {
                $contents .= $this->getFileContents($file) . "\n";
            }
        }
        return $contents;
    }
    protected function _processDependency($dependency)
    {
        static $vpsDependencies;
        if(!isset($vpsDependencies)) {
            $vpsDependencies = new Zend_Config_Ini(VPS_PATH.'/Vps_js/dependencies.ini', 'dependencies');
        }
        if (isset($dependency->dep)) {
            foreach ($dependency->dep as $x=>$d) {
                if (!isset($vpsDependencies->$d)) {
                    require_once 'Vps/Exception.php';
                    throw new Vps_Exception("Can't resolve dependency '$d'.");
                }
                $this->_processDependency($vpsDependencies->$d);
            }
        }
        if (isset($dependency->files)) {
            foreach ($dependency->files as $file) {
                if (substr($file, -2)=="/*") {
                    $pathType = substr($file, 0, strpos($file, '/'));
                    if (!isset($this->_paths[$pathType])) {
                        require_once 'Vps/Exception.php';
                        throw new Vps_Exception("JS-Path-Type '$pathType' not found in config.");
                    }
                    $file = substr($file, strpos($file, '/')); //pathtype abschneiden
                    $file = substr($file, 0, -1); //* abschneiden
                    $path = $this->_paths[$pathType].$file;
                    if (!file_exists($path)) {
                        require_once 'Vps/Exception.php';
                        throw new Vps_Exception("Path '$path' does not exist.");
                    }
                    $DirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
                    foreach ($DirIterator as $file) {
                        if (!preg_match('#/\\.svn/#', $file->getPathname())
                            && (substr($file->getPathname(), -3) == '.js'
                                || substr($file->getPathname(), -4) == '.css')) {
                            $f = $file->getPathname();
                            $f = substr($f, strlen($this->_paths[$pathType]));
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