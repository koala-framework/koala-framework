<?php
class Kwc_Root_Category_Trl_GeneratorForm extends Kwc_Abstract_Form
{
    protected $_modelName = 'Kwc_Root_Category_Trl_GeneratorModel';

    protected function _initFields()
    {
        $fields = $this->fields;
        $fields->add(new Kwf_Form_Field_TextField('name', trlKwf('Name of Page')))
            ->setAllowBlank(false);

        $fs = $fields->add(new Kwf_Form_Container_FieldSet('name', trlKwf('Name of Page')))
            ->setTitle(trlKwf('Custom Filename'))
            ->setCheckboxName('custom_filename')
            ->setCheckboxToggle(true);
        $fs->add(new Kwf_Form_Field_TextField('filename', trlKwf('Filename')))
            ->setAllowBlank(false)
            ->setVtype('alphanum');
    }
}
