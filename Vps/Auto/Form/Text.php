<?php
class Vps_Auto_Form_Text extends Vps_Auto_Form
{
    public function __construct($name = null, $id = null)
    {
        parent::__construct($name, $id);
        $this->setTable(new Vpc_Simple_Text_IndexModel());
        $this->fields->add(new Vps_Auto_Field_TextArea('content'))
            ->setFieldLabel('Content')
            ->setHeight(225)
            ->setWidth(450);
    }
}