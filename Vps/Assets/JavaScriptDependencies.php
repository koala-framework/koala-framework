<?php
class Vps_Assets_JavaScriptDependencies
{
    private $_files = array();
    private $_paths;
    
    public function __construct($paths)
    {
        if (!is_array($paths)) {
            $paths = $paths->toArray();
        }
        $this->_paths = $paths;
    }
    
    private function _getFilePath($file, $http=false)
    {
        $pathType = substr($file, 0, strpos($file, '/'));
        if (!isset($this->_paths[$pathType])) {
            throw new Vps_Exception("JS-Path-Type '$pathType' not found in config.");
        }
        if (!$http) {
            $path = $this->_paths[$pathType]['file'] . substr($file, strpos($file, '/') + 1);
            if(!file_exists($path)) {
                throw new Vps_Exception("JS-File '$path' does not exist.");
            }
        } else {
            $path = $this->_paths[$pathType]['http'].substr($file, strpos($file, '/') + 1);
        }
        return $path;
    }

    public function getFiles()
    {
        return $this->_files;
    }
    
    public function getFilePaths()
    {
        $paths = array();
        foreach ($this->_files as $file) {
            $paths[] = $this->_getFilePath($file);
        }
        return $paths;
    }

    public function getHttpFiles()
    {
        $paths = array();
        foreach ($this->_files as $file) {
            $paths[] = $this->_getFilePath($file, true);
        }
        return $paths;
    }
    
    private function _pack($contents)
    {
        $packer = new Vps_Assets_JavaScriptPacker($contents, 'Normal', true, false);
        return $packer->pack();
    }

    public function getPackedAll()
    {
        $frontendOptions = array(
            'lifetime' => null
        );
        $backendOptions = array(
            'cache_dir' => 'application/cache/assets/'
        );
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

        if (!$contents = $cache->load('jsAllPacked')) {
            $contents = $this->_pack($this->getContentsAll());
            $cache->save($contents, 'jsAllPacked');
        }

        return $contents;
    }

    public function getFileContents($file)
    {
        return file_get_contents($this->_getFilePath($file));
    }

    public function getContentsAll()
    {
        $contents = '';
        foreach($this->_files as $file) {
            $contents .= $this->getFileContents($file) . "\n";
        }
        return $contents;
    }
    public function addDependencies($dependencies)
    {
        foreach($dependencies as $d) {
            $this->_processDependency($d);
        }
    }

    protected function _processDependency($dependency)
    {
        static $vpsDependencies;
        if(!isset($vpsDependencies)) {
            $vpsDependencies = new Zend_Config_Ini(VPS_PATH.'Vps_js/dependencies.ini', 'dependencies');
        }
        if (isset($dependency->dep)) {
            foreach ($dependency->dep as $x=>$d) {
                if (!isset($vpsDependencies->$d)) {
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
                        throw new Vps_Exception("JS-Path-Type '$pathType' not found in config.");
                    }
                    $file = substr($file, strpos($file, '/') + 1); //pathtype abschneiden
                    $file = substr($file, 0, -1); //* abschneiden
                    $path = $this->_paths[$pathType]['file'].$file;
                    $DirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
                    foreach ($DirIterator as $file) {
                        if (!preg_match('#/\\.svn/#', $file->getPathname())
                            && substr($file->getPathname(), -3) == '.js') {
                            $f = $file->getPathname();
                            $f = substr($f, strlen($this->_paths[$pathType]['file']) - 1);
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