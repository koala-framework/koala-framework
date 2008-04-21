<?php
class Vpc_Composite_TextImage_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function _initFields()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');

        // Text
        $form = Vps_Auto_Vpc_Form::createComponentForm('text', $classes['text']);
        $form->setComponentIdTemplate('{0}-text');

        $fieldset = new Vps_Auto_Container_FieldSet(trlVps('Text'));
        $fieldset->add($form);
        $fieldset->add(new Vps_Auto_Field_ComboBox('image_position', trlVps('Position of Image')))
            ->setValues(array('left' => trlVps('Left'), 'right' => trlVps('Right'), 'alternate' => trlVps('Alternate')))
            ->setTriggerAction('all')
            ->setEditable(false);
        $this->_form->add($fieldset);

        // Image
        $form = Vps_Auto_Vpc_Form::createComponentForm('image', $classes['image']);
        $form->setComponentIdTemplate('{0}-image');
        $this->_form->add(new Vps_Auto_Container_FieldSet(trlVps('Image')))
            ->add($form);
    }
}
