<?php
class Kwf_Session_Namespace extends Zend_Session_Namespace
{
    public function __construct($namespace = 'Default', $singleInstance = false)
    {
        parent::__construct($namespace, $singleInstance);
        Kwf_Session::afterStart();
    }
}
