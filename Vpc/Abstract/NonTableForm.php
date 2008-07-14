<?php
class Vpc_Abstract_NonTableForm extends Vps_Form_NonTableForm
{
    public function __construct($name, $class)
    {
        $this->setProperty('class', $class);
        parent::__construct($name);
    }
}
