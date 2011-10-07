<?php
class Kwc_Basic_Flash_Upload_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->setLabelWidth(120);

        $this->add(new Kwf_Form_Field_File('FileMedia', trlKwf('swf File')));

        $this->add(new Kwf_Form_Field_NumberField('width', trlKwf('Width')))
                ->setMinValue(0)
                ->setMaxValue(9999)
                ->setWidth(75)
                ->setAllowEmpty(false);
        $this->add(new Kwf_Form_Field_NumberField('height', trlKwf('Height')))
                ->setMinValue(0)
                ->setMaxValue(9999)
                ->setWidth(75)
                ->setAllowEmpty(false);
        $this->add(new Kwf_Form_Field_Checkbox('allow_fullscreen', trlKwf('Allow fullscreen')));
        $this->add(new Kwf_Form_Field_Checkbox('menu', trlKwf('Menu')));

        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Flash variables')));
        $mf = $fs->add(new Kwf_Form_Field_MultiFields('FlashVars'));
        $mf->fields->add(new Kwf_Form_Field_TextField('key', trlKwf('Variable name')))
            ->setLabelWidth(120);
        $mf->fields->add(new Kwf_Form_Field_TextField('value', trlKwf('Value')))
            ->setLabelWidth(120);
    }
}
