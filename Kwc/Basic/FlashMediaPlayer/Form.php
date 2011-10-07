<?php
class Vpc_Basic_FlashMediaPlayer_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->fields->add(new Vps_Form_Field_File('FileMedia', trlVps('Element')))
            ->setDirectory('BasicFlashMediaPlayer');

        $this->fields->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
                ->setMinValue(1)
                ->setMaxValue(9999);
        $this->fields->add(new Vps_Form_Field_NumberField('height', trlVps('Height')))
                ->setMinValue(1)
                ->setMaxValue(9999);
        $this->fields->add(new Vps_Form_Field_Checkbox('autostart', trlVps('Start automatically')));
        $this->fields->add(new Vps_Form_Field_Checkbox('loop', trlVps('Loop')));
    }
}
