<?php
class Vpc_Decorator_Page_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->fields->add(new Vps_Form_Field_TextField('text', trlVps('Text')));
    }
}
