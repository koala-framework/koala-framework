<?php
class Vps_Assets_Dependencies
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
                    $DirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
                    foreach ($DirIterator as $file) {
                        if (!preg_match('#/\\.svn/#', $file->getPathname())
                            && substr($file->getPathname(), -3) == '.js') {
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