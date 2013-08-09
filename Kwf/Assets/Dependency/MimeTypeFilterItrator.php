<?php
class Kwf_Assets_Dependency_MimeTypeFilterItrator extends FilterIterator
{
    protected $_mimeType;
    public function __construct(Iterator $iterator, $mimeType)
    {
        $this->_mimeType = $mimeType;
        parent::__construct($iterator);
    }

    public function accept()
    {
        return $this->current()->getMimeType() == $this->_mimeType;
    }
}
