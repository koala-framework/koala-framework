<?php
class Vpc_Basic_Html_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $pageId = null, $componentKey = null)
    {
        parent::__construct($class, $pageId, $componentKey);
        $this->fields->add(new Vps_Auto_Field_TextArea('content'))
            ->setFieldLabel('Content')
            ->setHeight(225)
            ->setWidth(450);
    }
}
