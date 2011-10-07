<?php
class Kwc_Abstract_NonTableForm extends Kwf_Form_NonTableForm
{
    public function __construct($name, $class)
    {
        $this->setProperty('class', $class);
        parent::__construct($name);
    }
}
