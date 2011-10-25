<?php
class Kwf_Collection_Iterator_Recursive extends Kwf_Collection_Iterator
    implements RecursiveIterator
{
    public function hasChildren()
    {
        return $this->current()->hasChildren();
    }

    public function getChildren()
    {
        return new Kwf_Collection_Iterator_Recursive($this->current()->getChildren());
    }
}
