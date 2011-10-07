<?php
class Vpc_Abstract_FrontendForm extends Vps_Form
{
    public function __construct($name, $class)
    {
        $this->setClass($class);
        parent::__construct($name);
    }
}
