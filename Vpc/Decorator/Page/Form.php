<?php
class Vpc_Decorator_Page_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);
        $this->fields->add(new Vps_Auto_Field_TextField('text', trlVps('Text')));
    }
}
