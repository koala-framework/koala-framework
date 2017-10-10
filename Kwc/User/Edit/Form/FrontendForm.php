<?php
class Kwc_User_Edit_Form_FrontendForm extends Kwf_Form
{
    protected $_newUserRow;

    protected function _init()
    {
        parent::_init();
        $this->setModel(Kwf_Registry::get('userModel')->getEditModel());
    }

    public function addUserForms($detailsClass, $forms)
    {
        $generators = Kwc_Abstract::getSetting($detailsClass, 'generators');
        $classes = $generators['child']['component'];
        foreach ($classes as $component => $class) {
            if ($forms == 'all' || in_array($component, $forms)) {
                $form = Kwc_Abstract_Form::createChildComponentForm($detailsClass, '-'.$component);
                if ($form->getModel() && $form->getModel() instanceof Kwf_User_Model) {
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
