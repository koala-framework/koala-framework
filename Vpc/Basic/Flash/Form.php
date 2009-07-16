<?php
class Vpc_Basic_Flash_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->setLabelWidth(120);

        $fs = $this->fields->add(new Vps_Form_Container_FieldSet(trlVps('Flash file')));
        $fs->add(new Vps_Form_Field_File('FileMedia', trlVps('Element')));
        $fs->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
                ->setMinValue(0)
                ->setMaxValue(9999)
                ->setWidth(75)
                ->setAllowEmpty(false);
        $fs->add(new Vps_Form_Field_NumberField('height', trlVps('Height')))
                ->setMinValue(0)
                ->setMaxValue(9999)
                ->setWidth(75)
                ->setAllowEmpty(false);

        $fs = $this->fields->add(new Vps_Form_Container_FieldSet(trlVps('Flash variables')));
        $mf = $fs->add(new Vps_Form_Field_MultiFields('FlashVars'));
        $mf->fields->add(new Vps_Form_Field_TextField('key', trlVps('Variable name')))
            ->setLabelWidth(120);
        $mf->fields->add(new Vps_Form_Field_TextField('value', trlVps('Value')))
            ->setLabelWidth(120);
    }
}
