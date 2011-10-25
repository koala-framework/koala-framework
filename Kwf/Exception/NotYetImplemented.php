<?php
class Kwf_Exception_NotYetImplemented extends Kwf_Exception
{
    public function __construct($message = 'This functionality is not yet implemented')
    {
        parent::__construct($message);
    }
}
