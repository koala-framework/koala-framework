<?php
class Vps_Form_Field_Select_Form extends Vps_Form_Field_Abstract_Form
{
    protected function _init()
    {
        parent::_init();

        $mf = new Vps_Form_Field_MultiFields('values');
        $mf->fields->add(new Vps_Form_Field_TextField('value', trlVps('Value')));
        $mf->setModel(new Vps_Model_FieldRows(array(
            'fieldName' => 'values'
        )));

        $this->add(new Vps_Form_Container_FieldSet(trlVps('Values')))
            ->add($mf);

        $this->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
            ->setWidth(50);
        $this->add(new Vps_Form_Field_TextField('value', trlVps('Default Value')))
            ->setWidth(150);
    }
}
