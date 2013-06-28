<?php
class Kwf_Iterator_ProgressBar extends IteratorIterator
{
    private $_progress;

    public function __construct(Traversable $iterator, Zend_ProgressBar_Adapter $adatper)
    {
        parent::__construct($iterator);
        $count = $iterator->count();
        $this->_progress = new Zend_ProgressBar($adatper, 0, $count);

    }

    public function rewind()
    {
        $this->_progress->update(0);
        return parent::rewind();
    }

    public function next()
    {
        $this->_progress->next();
        return parent::next();
    }
}
