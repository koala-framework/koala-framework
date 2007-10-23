<?php
class Vpc_Composite_TextImage_IndexController extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save' => true);

    public function _initFields()
    {
        // Text
        $fieldset = new Vps_Auto_Container_FieldSet('Text');
        $fieldset->add(new Vpc_Basic_Text_Form($this->component->text));
        $fieldset->add(new Vps_Auto_Field_ComboBox('image_position', 'Position of Image'))
            ->setValues(array('left' => 'Left', 'right' => 'Right', 'alternate' => 'Alternate'))
            ->setTriggerAction('all')
            ->setEditable(false);
        $this->_form->add($fieldset);

        // Image
        $this->_form->add(new Vps_Auto_Container_FieldSet('Image'))
            ->add(new Vpc_Basic_Image_Form($this->component->image));
    }
}