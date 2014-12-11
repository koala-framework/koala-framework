<?php
class Kwf_Assets_Dependency_File extends Kwf_Assets_Dependency_Abstract
{
    protected $_fileName;
    private $_mtimeCache;
    private $_fileNameCache;

    public function __construct($fileNameWithType)
    {
        if (substr($fileNameWithType, 0, 1) == '/') {
            throw new Kwf_Exception('Don\'t use absolute file names');
        }
        if (!$fileNameWithType) {
            throw new Kwf_Exception("Invalid filename");
        }
        $this->_fileName = $fileNameWithType;

        //check commented out, only required for debugging
        //if (!file_exists($this->getAbsoluteFileName())) {
        //    throw new Kwf_Exception("File not found: '$this->_fileName' ('{$this->getAbsoluteFileName()}')");
        //}
    }

    public function getContents($language)
    {
        return file_get_contents($this->getAbsoluteFileName());
    }

    public function getType()
    {
        return substr($this->_fileName, 0, strpos($this->_fileName, '/'));
    }

    public function getFileNameWithType()
    {
        return $this->_fileName;
    }

    protected static function _getAllPaths()
    {
        static $paths;
        if (!isset($paths)) {
            $cacheId = 'assets-file-paths';
            $paths = Kwf_Cache_SimpleStatic::fetch($cacheId);
            if ($paths === false) {
                $paths = array(
                    'webComponents' => 'components',
                    'webThemes' => 'themes',
                );
                $vendors[] = KWF_PATH; //required for kwf tests, in web kwf is twice in $vendors but that's not a problem
                $vendors[] = '.';
                $vendors = array_merge($vendors, glob(VENDOR_PATH."/*/*"));
                foreach ($vendors as $i) {
                    if (is_dir($i) && file_exists($i.'/dependencies.ini')) {
                        $c = new Zend_Config_Ini($i.'/dependencies.ini');
                        if ($c->config) {
                            $dep = new Zend_Config_Ini($i.'/dependencies.ini', 'config');
                            $pathType = (string)$dep->pathType;
                            if ($pathType) {
                                $paths[$pathType] = $i;
                            }
                        }
                    }
                }
                foreach (glob(VENDOR_PATH.'/bower_components/*') as $i) {
                    $type = substr($i, strlen(VENDOR_PATH.'/bower_components/'));
                    if (substr($type, -3) == '.js') $type = substr($type, 0, -3);
                    if (substr($type, -2) == 'js') $type = substr($type, 0, -2);
                    $paths[$type] = $i;
                }
                $paths['web'] = '.';
                Kwf_Cache_SimpleStatic::add($cacheId, $paths);
            }
        }
        return $paths;
    }

    public function getAbsoluteFileName()
    {
        if (isset($this->_fileNameCache)) return $this->_fileNameCache;
        $paths = self::_getAllPaths();
        $pathType = $this->getType();
        $f = substr($this->_fileName, strpos($this->_fileName, '/'));
        if (isset($paths[$pathType])) {
            $f = $paths[$pathType].$f;
        } else if (file_exists($this->_fileName)) {
            $f = $this->_fileName;
        } else {
            throw new Kwf_Exception("Unknown path type: '$pathType' for '$this->_fileName'");
        }
        $this->_fileNameCache = $f;

        return $f;
    }

    public function getMTime()
    {
        if (!isset($this->_mtimeCache)) {
            $f = $this->getAbsoluteFileName();
            if ($f) {
                $this->_mtimeCache = filemtime($f);
            }
        }
        return $this->_mtimeCache;
    }

    public static function createDependency($fileName, Kwf_Assets_ProviderList_Abstract $providerList)
    {
        if (substr($fileName, 0, 7) == 'http://' || substr($fileName, 0, 8) == 'https://') {
            $ret = new Kwf_Assets_Dependency_HttpUrl($fileName);
        } else if (substr($fileName, -3) == '.js') {
            $ret = new Kwf_Assets_Dependency_File_Js($fileName);
        } else if (substr($fileName, -4) == '.css') {
            $ret = new Kwf_Assets_Dependency_File_Css($fileName);
        } else if (substr($fileName, -9) == '.printcss') {
            $ret = new Kwf_Assets_Dependency_File_PrintCss($fileName);
        } else if (substr($fileName, -5) == '.scss') {
            $ret = new Kwf_Assets_Dependency_File_Scss($fileName);
        } else if (substr($fileName, -2) == '/*') {
            $pathType = substr($fileName, 0, strpos($fileName, '/'));
            $fileName = substr($fileName, strpos($fileName, '/')); //pathtype abschneiden
            $fileName = substr($fileName, 0, -1); // /* abschneiden

            $paths = self::_getAllPaths();
            $path = $paths[$pathType].$fileName;
            if (!file_exists($path)) {
                throw new Kwf_Exception("Path '$path' does not exist.");
            }
            $files = array();
            $it = new RecursiveDirectoryIterator($path);
            $it = new Kwf_Iterator_Filter_HiddenFiles($it);
            $it = new RecursiveIteratorIterator($it);
            $it = new Kwf_Iterator_Filter_FileExtension($it, array('js', 'css'));
            foreach ($it as $file) {
                $f = $file->getPathname();
                $f = substr($f, strlen($paths[$pathType]));
                $f = $pathType . $f;
                $files[] = self::createDependency($f, $providerList);
            }
            $ret = new Kwf_Assets_Dependency_Dependencies($files, $fileName.'*');
        } else {
            throw new Kwf_Exception("unknown file type: ".$fileName);
        }
        return $ret;
    }

    public function __toString()
    {
        return $this->_fileName;
    }

    protected function _getComponentCssClass()
    {
        $cssClass = $this->getAbsoluteFileName();
        if (substr($cssClass, 0, 2) == './') $cssClass = substr($cssClass, 2);

        $paths = self::_getAllPaths();
        foreach ($paths as $i) {
            if ($i && substr($cssClass, 0, strlen($i)+1) == $i.'/') {
                $cssClass = substr($cssClass, strlen($i)+1);
            }
        }
        if (substr($cssClass, -4) == '.css') {
            $cssClass = substr($cssClass, 0, -4);
        }
        if (substr($cssClass, -5) == '.scss') {
            $cssClass = substr($cssClass, 0, -5);
        }
        if (substr($cssClass, -9) == '.defer.js') {
            $cssClass = substr($cssClass, 0, -9);
        }
        if (substr($cssClass, -3) == '.js') {
            $cssClass = substr($cssClass, 0, -3);
        }
        if (substr($cssClass, -9) == '.printcss') {
            $cssClass = substr($cssClass, 0, -9);
        }
        if (substr($cssClass, -10) == '/Component') {
            $cssClass = substr($cssClass, 0, -10);
        } else if (substr($cssClass, -7) == '/Master') {
            $cssClass = substr($cssClass, 0, -7);
            $cssClass = 'master'.$cssClass;
        } else {
            $cssClass = false;
        }
        if ($cssClass) {
            $cssClass = str_replace('/', '', $cssClass);
            return strtolower(substr($cssClass, 0, 1)) . substr($cssClass, 1);
        }
        return null;
    }

    private static function _getAbsolutePath($path)
    {
        if (substr($path, 0, 1)=='.') $path = getcwd().'/'.$path;
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        if (DIRECTORY_SEPARATOR == '\\') {
            //windows
            return implode(DIRECTORY_SEPARATOR, $absolutes);
        } else {
            return DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $absolutes);
        }
    }

    public static function getPathWithTypeByFileName($fileName)
    {
        $paths = self::_getAllPaths();
        $fileName = self::_getAbsolutePath($fileName);
        $fileName = str_replace(DIRECTORY_SEPARATOR, '/', $fileName);
        foreach ($paths as $k=>$p) {
            if ($p == '.') $p = getcwd();
            if (substr($p, 0, 7) == 'vendor/') $p = getcwd().'/'.$p;
            $p = str_replace(DIRECTORY_SEPARATOR, '/', $p);
            if (substr($fileName, 0, strlen($p)) == $p) {
                return $k.substr($fileName, strlen($p));
            }
        }
        return false;
    }
}
