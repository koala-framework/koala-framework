<?php
class Vpc_Composite_LinkImage_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_permissions = array('add', 'save', 'delete', 'edit');

    public function _initFields()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');

        // Image
        $form = new Vpc_Basic_Image_Form('image', $classes['image']);
        $form->setComponentIdTemplate('{0}-image');
        $this->_form->add(new Vps_Auto_Container_FieldSet('Image'))
            ->add($form);

        // Link
        $form = new Vpc_Basic_LinkTag_Form('link', $classes['link']);
        $form->setComponentIdTemplate('{0}-link');
        $this->_form->add(new Vps_Auto_Container_FieldSet('Link'))
            ->add($form);

    }
}
