<?php
class Vpc_Basic_Html_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);
        $this->fields->add(new Vps_Auto_Field_TextArea('content'))
            ->setFieldLabel(trlVps('Content'))
            ->setHeight(225)
            ->setWidth(450);
    }
}
