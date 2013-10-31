<?php
/**
 * FilterIterator that filters all hidden files an directories
 *
 * Usage Example:
 * $it = new RecursiveDirectoryIterator('.');
 * $it = new Kwf_Iterator_Filter_HiddenFiles($it);
 * $it = new RecursiveIteratorIterator($it);
 */
class Kwf_Iterator_Filter_HiddenFiles extends RecursiveFilterIterator
{
    public function getChildren()
    {
        return new self($this->getInnerIterator()->getChildren());
    }

    public function accept()
    {
        return substr($this->current()->getFilename(), 0, 1) != '.';
    }
}
