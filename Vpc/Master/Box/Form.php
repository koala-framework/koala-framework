<?php
class Vpc_Master_Box_Form extends Vps_Form_NonTableForm
{
    protected function _getRowByParentRow($parentRow)
    {
        return $parentRow;
    }
    
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        foreach (Vpc_Abstract::getChildComponentClasses($class) as $key => $component) {
            $form = Vpc_Abstract_Form::createChildComponentForm($class, "-$key");
            if ($form) {
                $form->setBaseCls('x-plain');
                try {
                    $title = Vpc_Abstract::getSetting($component, 'componentName');
                    $fieldset = new Vps_Form_Container_FieldSet($title);
                    $fieldset->add($form);
                    $this->add($fieldset);
                } catch (Vps_Exception $e) {
                    $this->add($form);
                }
            }
        }
    }
}
