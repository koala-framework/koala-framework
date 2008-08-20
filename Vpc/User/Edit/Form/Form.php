<?php
class Vpc_User_Edit_Form_Form extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->setTable(Zend_Registry::get('userModel'));
    }
    public function initFields()
    {
        parent::initFields();

        if ($this->getUserEditForms()) {
            $forms = $this->getUserEditForms();
            $detailsClass = $this->getUserDetailsComponent();
            if (!$detailsClass) {
                throw new Vps_Exception("If you use userEditForms you must also set userDetailsComponent");
            }
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
        } else {
            $this->add(new Vpc_User_Detail_General_Form('general'));
        }
    }
}
