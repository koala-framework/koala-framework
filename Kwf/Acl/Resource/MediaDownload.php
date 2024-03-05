<?php
class Kwf_Acl_Resource_MediaDownload extends Zend_Acl_Resource
{
    private $_mimeTypePattern;
    private $_filenamePattern;
    private $_maxFilesize;

    public function __construct($resourceId, $mimeTypePattern = false, $filenamePattern = false, $maxFilesize = false)
    {
        $this->_mimeTypePattern = $mimeTypePattern;
        $this->_filenamePattern = $filenamePattern;
        $this->_maxFilesize = $maxFilesize;
        parent::__construct($resourceId);
    }

    public function getMimeTypePattern()
    {
        return $this->_mimeTypePattern;
    }
    public function getFilenamePattern()
    {
        return $this->_filenamePattern;
    }
    public function getMaxFilesize()
    {
        return $this->_maxFilesize;
    }
}
