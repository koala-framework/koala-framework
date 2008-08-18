<?php
class Vpc_User_Edit_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('edit account');
        $ret['generators']['child']['component']['success'] = 'Vpc_User_Edit_Form_Success_Component';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }
    
    protected function _initForm()
    {
        $parentClass = $this->getData()->parent->componentClass;
        $forms = Vpc_Abstract::getSetting($parentClass, 'forms');
        
        $table = Zend_Registry::get('userModel');
        $user = $table->getAuthedUser();
        if (!$user) return parent::_initForm();
        $id = $user->id;
        $this->_form = new Vps_Form('user');
        $this->_form->setTable($table);
        $this->_form->setId($id);
        $detailsComponent = $this->getData()->parent->parent->getChildComponent('_' . $id);
        $class = $detailsComponent->componentClass;
        $generators = Vpc_Abstract::getSetting($class, 'generators');
        foreach ($generators['child']['component'] as $component => $class) {
            if ($forms == 'all' || in_array($class, $forms)) {
                $form = Vpc_Abstract_Form::createComponentForm($class, $component);
                if ($form) {
                    $form->setId($detailsComponent->dbId . '-' . $component);
                    $title = trlVps(ucfirst($component));
                    $this->_form->add(new Vps_Form_Container_FieldSet($title))
                        ->add($form);
                }
            }
        }
    }
}
