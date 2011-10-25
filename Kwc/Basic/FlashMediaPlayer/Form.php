<?php
class Kwc_Basic_FlashMediaPlayer_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->fields->add(new Kwf_Form_Field_File('FileMedia', trlKwf('Element')))
            ->setDirectory('BasicFlashMediaPlayer');

        $this->fields->add(new Kwf_Form_Field_NumberField('width', trlKwf('Width')))
                ->setMinValue(1)
                ->setMaxValue(9999);
        $this->fields->add(new Kwf_Form_Field_NumberField('height', trlKwf('Height')))
                ->setMinValue(1)
                ->setMaxValue(9999);
        $this->fields->add(new Kwf_Form_Field_Checkbox('autostart', trlKwf('Start automatically')));
        $this->fields->add(new Kwf_Form_Field_Checkbox('loop', trlKwf('Loop')));
    }
}
