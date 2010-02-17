<?php
class Vpc_Root_Category_Trl_GeneratorController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vpc_Root_Category_Trl_GeneratorModel';
    protected $_permissions = array('save');

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_form->setId($this->_getParam('id'));
    }

    protected function _initFields()
    {
        $fields = $this->_form->fields;
        $fields->add(new Vps_Form_Field_TextField('name', trlVps('Name of Page')))
            ->setAllowBlank(false);

        $fs = $fields->add(new Vps_Form_Container_FieldSet('name', trlVps('Name of Page')))
            ->setTitle(trlVps('Custom Filename'))
            ->setCheckboxName('custom_filename')
            ->setCheckboxToggle(true);
        $fs->add(new Vps_Form_Field_TextField('filename', trlVps('Filename')))
            ->setAllowBlank(false)
            ->setVtype('alphanum');

        $fields->add(new Vps_Form_Field_TextField('tags', trlVps('Tags')));
    }
}
