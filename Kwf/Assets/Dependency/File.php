<?php
class Kwf_Assets_Dependency_File extends Kwf_Assets_Dependency_Abstract
{
    protected $_fileName;
    private $_fileNameCache;
    private $_sourceStringCache;

    public function __construct(Kwf_Assets_ProviderList_Abstract $providerList, $fileNameWithType)
    {
        parent::__construct($providerList);
        if (substr($fileNameWithType, 0, 1) == '/') {
            throw new Kwf_Exception('Don\'t use absolute file names');
        }
        if (!$fileNameWithType) {
            throw new Kwf_Exception("Invalid filename");
        }
        $this->_fileName = $fileNameWithType;
        if (strpos($fileNameWithType, '\\') !== false) {
            throw new Kwf_Exception("Infalid filename, must not contain \\, use / instead");
        }

        //check commented out, only required for debugging
        //if (!file_exists($this->getAbsoluteFileName())) {
        //    throw new Kwf_Exception("File not found: '$this->_fileName' ('{$this->getAbsoluteFileName()}')");
        //}
    }

    public function getContentsSource()
    {
        return array(
            'type' => 'file',
            'file' => $this->getAbsoluteFileName(),
        );
    }

    public function getContentsSourceString()
    {
        if (!isset($this->_sourceStringCache)) {
            $this->_sourceStringCache = file_get_contents($this->getAbsoluteFileName());
        }
        return $this->_sourceStringCache;
    }

    public function getType()
    {
        return substr($this->_fileName, 0, strpos($this->_fileName, '/'));
    }

    public function getIdentifier()
    {
        return $this->_fileName;
    }

    public function getAbsoluteFileName()
    {
        if (isset($this->_fileNameCache)) return $this->_fileNameCache;
        $paths = $this->_providerList->getPathTypes();
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

    public function __toString()
    {
        return $this->_fileName;
    }
}
