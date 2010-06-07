<?php
class Vpc_Root_Category_Trl_GeneratorForm extends Vpc_Abstract_Form
{
    protected $_modelName = 'Vpc_Root_Category_Trl_GeneratorModel';

    protected function _initFields()
    {
        $fields = $this->fields;
        $fields->add(new Vps_Form_Field_TextField('name', trlVps('Name of Page')))
            ->setAllowBlank(false);

        $fs = $fields->add(new Vps_Form_Container_FieldSet('name', trlVps('Name of Page')))
            ->setTitle(trlVps('Custom Filename'))
            ->setCheckboxName('custom_filename')
            ->setCheckboxToggle(true);
        $fs->add(new Vps_Form_Field_TextField('filename', trlVps('Filename')))
            ->setAllowBlank(false)
            ->setVtype('alphanum');
    }
}
