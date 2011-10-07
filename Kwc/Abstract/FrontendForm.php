<?php
class Kwc_Abstract_FrontendForm extends Kwf_Form
{
    public function __construct($name, $class)
    {
        $this->setClass($class);
        parent::__construct($name);
    }
}
