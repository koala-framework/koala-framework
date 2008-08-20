<?php
class Vps_Collection_Iterator_Recursive extends Vps_Collection_Iterator
    implements RecursiveIterator
{
    public function hasChildren()
    {
        return $this->current()->hasChildren();
    }

    public function getChildren()
    {
        return new Vps_Collection_Iterator_Recursive($this->current()->getChildren());
    }
}
