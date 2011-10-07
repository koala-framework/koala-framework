<?php
class Kwf_Form_Field_Select_Form extends Kwf_Form_Field_Abstract_Form
{
    protected function _init()
    {
        parent::_init();

        $mf = new Kwf_Form_Field_MultiFields('values');
        $mf->fields->add(new Kwf_Form_Field_TextField('value', trlKwf('Value')));
        $mf->setModel(new Kwf_Model_FieldRows(array(
            'fieldName' => 'values'
        )));

        $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Values')))
            ->add($mf);

        $this->add(new Kwf_Form_Field_NumberField('width', trlKwf('Width')))
            ->setWidth(50);
        $this->add(new Kwf_Form_Field_TextField('value', trlKwf('Default Value')))
            ->setWidth(150);
    }
}
