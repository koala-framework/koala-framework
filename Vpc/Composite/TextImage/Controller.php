<?php
class Vpc_Composite_TextImage_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function _initFields()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');
        // Text
        $form = new Vpc_Basic_Text_Form($classes['text']);
        $form->setComponentIdTemplate('{0}-text');
        
        $fieldset = new Vps_Auto_Container_FieldSet('Text');
        $fieldset->add($form);
        $fieldset->add(new Vps_Auto_Field_ComboBox('image_position', 'Position of Image'))
            ->setValues(array('left' => 'Left', 'right' => 'Right', 'alternate' => 'Alternate'))
            ->setTriggerAction('all')
            ->setEditable(false);
        $this->_form->add($fieldset);

        // Image
        $form = new Vpc_Basic_Image_Form($classes['image']);
        $form->setComponentIdTemplate('{0}-image');
        $this->_form->add(new Vps_Auto_Container_FieldSet('Image'))
            ->add($form);
    }
}
