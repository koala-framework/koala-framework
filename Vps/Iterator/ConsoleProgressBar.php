<?php
class Vps_Iterator_ConsoleProgressBar extends IteratorIterator
{
    private $_progress;

    public function __construct(Traversable $iterator)
    {
        parent::__construct($iterator);
        $count = $iterator->count();
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_ETA));
        $this->_progress = new Zend_ProgressBar($c, 0, $count);

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
