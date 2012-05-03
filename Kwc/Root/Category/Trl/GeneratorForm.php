<?php
class Kwc_Root_Category_Trl_GeneratorForm extends Kwf_Form
{
    public function __construct($generator)
    {
        $this->setModel($generator->getModel());
        $this->setCreateMissingRow(true);
        parent::__construct();
    }
    protected function _initFields()
    {
        $fields = $this->fields;

        $fields->add(new Kwf_Form_Field_ShowField('original_name', trlKwf('Original  Name')))
            ->setData(new Kwf_Data_Trl_OriginalComponentFromData('name'));

        $fields->add(new Kwf_Form_Field_TextField('name', trlKwf('Name of Page')))
            ->setAllowBlank(false);

        $fs = $fields->add(new Kwf_Form_Container_FieldSet(trlKwf('Custom Filename')))
            ->setCheckboxName('custom_filename')
            ->setCheckboxToggle(true);
        $fs->add(new Kwf_Form_Field_TextField('filename', trlKwf('Filename')))
            ->setAllowBlank(false)
            ->setVtype('alphanum');
    }
}
