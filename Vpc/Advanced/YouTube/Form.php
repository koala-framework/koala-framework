<?php
class Vpc_Advanced_YouTube_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->fields->add(new Vps_Form_Field_TextField('url', trlVps('YouTube URL')))
            ->setWidth(400)
            ->setVtype('url');
        $this->fields->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
            ->setMinValue(1)
            ->setMaxValue(9999);
        $this->fields->add(new Vps_Form_Field_NumberField('height', trlVps('Height')))
            ->setMinValue(1)
            ->setMaxValue(9999);
    }
}
