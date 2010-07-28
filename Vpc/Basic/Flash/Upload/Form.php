<?php
class Vpc_Basic_Flash_Upload_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->setLabelWidth(120);

        $this->add(new Vps_Form_Field_File('FileMedia', trlVps('swf File')));

        $this->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
                ->setMinValue(0)
                ->setMaxValue(9999)
                ->setWidth(75)
                ->setAllowEmpty(false);
        $this->add(new Vps_Form_Field_NumberField('height', trlVps('Height')))
                ->setMinValue(0)
                ->setMaxValue(9999)
                ->setWidth(75)
                ->setAllowEmpty(false);
        $this->add(new Vps_Form_Field_Checkbox('allow_fullscreen', trlVps('Allow fullscreen')));
        $this->add(new Vps_Form_Field_Checkbox('menu', trlVps('Menu')));

        $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Flash variables')));
        $mf = $fs->add(new Vps_Form_Field_MultiFields('FlashVars'));
        $mf->fields->add(new Vps_Form_Field_TextField('key', trlVps('Variable name')))
            ->setLabelWidth(120);
        $mf->fields->add(new Vps_Form_Field_TextField('value', trlVps('Value')))
            ->setLabelWidth(120);
    }
}
