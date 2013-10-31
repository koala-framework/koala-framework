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

    public static function createDependency($fileName)
    {
        if (substr($fileName, 0, 7) == 'http://' || substr($fileName, 0, 8) == 'https://') {
            return new Kwf_Assets_Dependency_HttpUrl($fileName);
        } else if (substr($fileName, -3) == '.js') {
            return new Kwf_Assets_Dependency_File_Js($fileName);
        } else if (substr($fileName, -4) == '.css') {
            return new Kwf_Assets_Dependency_File_Css($fileName);
        } else if (substr($fileName, -9) == '.printcss') {
            return new Kwf_Assets_Dependency_File_PrintCss($fileName);
        } else if (substr($fileName, -5) == '.scss') {
            return new Kwf_Assets_Dependency_File_Scss($fileName);
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
                $files[] = self::createDependency($f);
            }
            return new Kwf_Assets_Dependency_Dependencies($files, $fileName.'*');
        }
        throw new Kwf_Exception("unknown file type: ".$fileName);
    }

    public function __toString()
    {
        $ret = $this->_fileName;
        if (!$ret) {
            $ret = parent::__toString();
        }
        return $ret;
    }
}
