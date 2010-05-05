<?php
class Vpc_Advanced_Imprint_VividPlanet_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        $this->setLabelWidth(200);
        $this->fields->add(new Vps_Form_Field_Checkbox('hide_webdesign', trlVps('Hide webdesign')));
        $this->fields->add(new Vps_Form_Field_Checkbox('is_isiweb', trlVps('IsiWeb')));
    }
}
