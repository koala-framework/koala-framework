<?php
class Vpc_Composite_SwitchDisplay_LinkText_Form extends Vpc_Basic_Textfield_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->getByName('content')->setFieldLabel(trlVps('Link text'));
    }
}
