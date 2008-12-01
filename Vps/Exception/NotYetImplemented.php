<?php
class Vps_Exception_NotYetImplemented extends Vps_Exception
{
    public function __construct($message = 'This functionality is not yet implemented')
    {
        parent::__construct($message);
    }
}
