<?php
class Vpc_Composite_TextImage_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function _initFields()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');

        // Text
        $form = Vpc_Abstract_Form::createComponentForm('text', $classes['text']);
        $form->setIdTemplate('{0}-text');

        $fieldset = new Vps_Form_Container_FieldSet(trlVps('Text'));
        $fieldset->add($form);
        $fieldset->add(new Vps_Form_Field_ComboBox('image_position', trlVps('Position of Image')))
            ->setValues(array('left' => trlVps('Left'), 'right' => trlVps('Right'), 'alternate' => trlVps('Alternate')))
            ->setTriggerAction('all')
            ->setEditable(false);
        $this->_form->add($fieldset);

        // Image
        $form = Vpc_Abstract_Form::createComponentForm('image', $classes['image']);
        $form->setIdTemplate('{0}-image');
        $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Image')))
            ->add($form);
    }
}
