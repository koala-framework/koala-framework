<?php
class Kwf_Assets_Dependency_File extends Kwf_Assets_Dependency_Abstract
{
    protected $_fileName;
    private $_mtimeCache;

    public function __construct($fileName)
    {
        $this->_fileName = $fileName;
    }

    public function getContents($language)
    {
        return file_get_contents($this->getFileName());
    }

    public function getFileName()
    {
        static $paths;
        if (!isset($paths)) $paths = Kwf_Config::getValueArray('path');

        $pathType = substr($this->_fileName, 0, strpos($this->_fileName, '/'));
        $f = substr($this->_fileName, strpos($this->_fileName, '/'));
        if (isset($paths[$pathType])) {
            $f = $paths[$pathType].$f;
        } else if (file_exists($this->_fileName)) {
            $f = $this->_fileName;
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
        return $f;
    }

    public function getMTime()
    {
        if (!isset($this->_mtimeCache)) {
            $f = $this->getFileName();
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

            static $paths;
            if (!isset($paths)) $paths = Kwf_Config::getValueArray('path');
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
        $ret = $this->_fileName;
        if (!$ret) {
            $ret = parent::__toString();
        }
        return $ret;
    }

    protected function _getComponentCssClass()
    {
        $cssClass = realpath($this->getFileName());

        static $paths;
        if (!isset($paths)) {
            $paths = Kwf_Config::getValueArray('path');
            foreach ($paths as &$p) {
                if (substr($p, 0, 1) == '.') $p = getcwd().substr($p, 1);
            }
            unset($paths['web']);
            $paths['webComponents'] = getcwd().'/components';
        }
        foreach ($paths as $i) {
            $i = realpath($i);
            if ($i && substr($cssClass, 0, strlen($i)) == $i) {
                $cssClass = substr($cssClass, strlen($i)+1);
            }
        }

        if (substr($cssClass, -4) == '.css') {
            $cssClass = substr($cssClass, 0, -4);
        }
        if (substr($cssClass, -5) == '.scss') {
            $cssClass = substr($cssClass, 0, -5);
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
}
