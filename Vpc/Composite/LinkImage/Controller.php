<?php
class Vpc_Composite_LinkImage_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_permissions = array('add', 'save', 'delete', 'edit');
    protected $_formName = 'Vpc_Abstract_NonTableForm';

    public function _initFields()
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->class, 'child');

        // Image
        $form = new Vpc_Basic_Image_Form('image', $classes['image']);
        $form->setIdTemplate('{0}-image');
        $this->_form->add(new Vps_Form_Container_FieldSet('Image'))
            ->add($form);

        // Link
        $form = new Vpc_Basic_LinkTag_Form('link', $classes['link']);
        $form->setIdTemplate('{0}-link');
        $this->_form->add(new Vps_Form_Container_FieldSet('Link'))
            ->add($form);

    }
}
