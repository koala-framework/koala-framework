<?php
class Vpc_User_Edit_Form_Form extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->setModel(Zend_Registry::get('userModel'));
    }

    public function addUserForms($detailsClass, $forms)
    {
        $generators = Vpc_Abstract::getSetting($detailsClass, 'generators');
        $classes = $generators['child']['component'];
        foreach ($classes as $component => $class) {
            if ($forms == 'all' || in_array($component, $forms)) {
                $form = Vpc_Abstract_Form::createChildComponentForm($detailsClass, '-'.$component);
                if ($form->getModel() && $form->getModel()->getTable() instanceof Vps_Model_User_Users) {
                    $form->setIdTemplate("{0}");
                } else {
                    $form->setIdTemplate("users_{0}-$component");
                }
                if ($form) {
                    $this->add($form);
                }
            }
        }
    }
}
