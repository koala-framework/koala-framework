<?php
class Kwf_Assets_Dependency_Iterator_UniqueFilter extends RecursiveFilterIterator
{
    private $_processed = array();
    public function accept()
    {
        $cur = $this->current();
        if (!isset($this->_processed[spl_object_hash($cur)])) {
            $this->_processed[spl_object_hash($cur)] = true;
            return true;
        }
        return false;
    }

    public function getChildren()
    {
        $ret = new self($this->getInnerIterator()->getChildren());
        $ret->_processed = &$this->_processed;
        return $ret;
    }
}
