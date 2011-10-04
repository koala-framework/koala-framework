<?php
class Vps_Iterator_Filter_FileExtension extends FilterIterator
{
    protected $it;
    private $_extension;

    function __construct($iterator, $extension)
    {
        $this->it = $iterator;
        $this->_extension = $extension;
        parent::__construct($this->it);
    }

    function accept()
    {
        return ($this->it->getSubIterator()->isFile() &&
            preg_match('/^.+\.'.$this->_extension.'$/i', $this->it->getSubIterator()->getFilename())
        );
    }
}
