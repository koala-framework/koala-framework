<?php
class Kwf_Iterator_ConsoleProgressBar extends Kwf_Iterator_ProgressBar
{
    public function __construct(Traversable $iterator)
    {
        parent::__construct($iterator, new Zend_ProgressBar_Adapter_Console());
    }
}
