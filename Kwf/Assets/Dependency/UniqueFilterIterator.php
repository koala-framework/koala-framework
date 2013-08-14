<?php
class Kwf_Assets_Dependency_UniqueFilterIterator extends RecursiveFilterIterator
{
    private $_processed = array();
    public function accept()
    {
        $cur = $this->current();
        if (!in_array($cur, $this->_processed, true)) {
            $this->_processed[] = $cur;
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
