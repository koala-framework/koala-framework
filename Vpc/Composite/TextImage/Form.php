<?php
class Vpc_Composite_TextImage_Form extends Vpc_Abstract_Form
{
    protected function _init()
    {
        parent::_init();

        // Text
        $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), '-text');

        $fieldset = new Vps_Form_Container_FieldSet(trlVps('Text'));
        $fieldset->add($form);
        $fieldset->add(new Vps_Form_Field_ComboBox('image_position', trlVps('Position of Image')))
            ->setValues(array('left' => trlVps('Left'), 'right' => trlVps('Right'), 'alternate' => trlVps('Alternate')))
            ->setTriggerAction('all')
            ->setEditable(false);
        $this->add($fieldset);

        // Image
        $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), '-image');
        $this->add(new Vps_Form_Container_FieldSet(trlVps('Image')))
            ->add($form);
    }
}
