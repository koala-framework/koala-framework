<?php
class Vpc_Basic_Download_Form extends Vpc_Abstract_Composite_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $this->add(new Vps_Form_Field_TextArea('infotext', trlVps('Infotext')))
            ->setWidth(300)
            ->setGrow(true);
    }
}
