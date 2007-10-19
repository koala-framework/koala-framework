<?php
class Vpc_Composite_Images_EditController extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save' => true);

    public function _initFields()
    {
        // Image
        $this->_form->add(new Vps_Auto_Container_FieldSet('Image'))
            ->add(new Vpc_Basic_Image_Form($this->component));
/*
        // Enlarged Image
        $imagebig = new Vpc_Basic_Image_Form($imagebig);
        $imagebig->fields->getByName('vps_upload_id')->setFileFieldLabel('File (optional)');
        $this->_form->add(new Vps_Auto_Container_FieldSet('Enlarged Image (will be shown as popup)'))
            ->setCheckboxToggle(true)
            ->setCollapsed(!$this->component->getSetting('enlarge'))
            ->add($imagebig);
*/
    }
}