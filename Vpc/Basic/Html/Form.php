<?php
class Vpc_Basic_Html_Form extends Vps_Auto_Vpc_Form
{
    public function __construct(Vpc_Basic_Html_Component $component)
    {
        parent::__construct($component);
        $this->fields->add(new Vps_Auto_Field_TextArea('content'))
            ->setFieldLabel('Content')
            ->setHeight(225)
            ->setWidth(450);
    }
}