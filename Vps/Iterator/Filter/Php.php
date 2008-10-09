<?php
class Vps_Iterator_Filter_Php extends FilterIterator
{
    protected $it;

    function __construct($iterator)
    {
        $this->it = $iterator;
        parent::__construct($this->it);
    }

    function accept()
    {
        return ($this->it->getSubIterator()->isFile() &&
            preg_match('/^.+\.php$/i', $this->it->getSubIterator()->getFilename())
        );
    }
}
