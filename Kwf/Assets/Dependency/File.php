<?php
class Kwf_Assets_Dependency_File extends Kwf_Assets_Dependency_Abstract
{
    protected $_fileName;
    public function __construct($fileName)
    {
        $this->_fileName = $fileName;
    }

    public function getContents($language)
    {
        $f = substr($this->_fileName, strpos($this->_fileName, '/'));
        if (substr($this->_fileName, 0, strpos($this->_fileName, '/')) == 'kwf') {
            $f = KWF_PATH.$f;
        } else if (file_exists($this->_fileName)) {
            $f = $this->_fileName;
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
        return file_get_contents($f);
    }

    public function getMTime()
    {
        $f = $this->getFileName();
        if ($f) {
            return filemtime($f);
        }
    }

    public static function createDependency($fileName)
    {
        if (substr($fileName, -3) == '.js') {
            return new Kwf_Assets_Dependency_File_Js($fileName);
        }else if (substr($fileName, -4) == '.css') {
            return new Kwf_Assets_Dependency_File_Css($fileName);
        }else if (substr($fileName, -9) == '.printcss') {
            return new Kwf_Assets_Dependency_File_PrintCss($fileName);
        }else if (substr($fileName, -4) == '.scss') {
            return new Kwf_Assets_Dependency_File_Scss($fileName);
        }
        throw new Kwf_Exception("unknown file type: ".$fileName);
    }
}
