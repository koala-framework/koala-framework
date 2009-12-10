<?php
class Vpc_Basic_Textfield_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->fields->add(new Vps_Form_Field_TextField('content', trlVps('Content')))
            ->setWidth(400);
    }
}
