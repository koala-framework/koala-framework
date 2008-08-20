<?php
class Vpc_User_Edit_Form_Form extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->setTable(Zend_Registry::get('userModel'));
    }
    public function addUserForms($detailsClass, $forms)
    {
        foreach (Vpc_Abstract::getChildComponentClasses($detailsClass, 'child') as $component => $class) {
            if ($forms == 'all' || in_array($component, $forms)) {
                $form = Vpc_Abstract_Form::createChildComponentForm($detailsClass, '-'.$component);
                if ($form) {
                    $this->add($form);
                    /*
                    $title = Vpc_Abstract::getSetting($class, 'componentName');
                    $this->add(new Vps_Form_Container_FieldSet($title))
                        ->add($form);
                    */
                }
            }
        }
    }
}
