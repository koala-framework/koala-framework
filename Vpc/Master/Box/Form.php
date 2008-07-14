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
            try {
                $form = Vpc_Abstract_Form::createComponentForm($key, $component);
                if ($form instanceof Vpc_Abstract_FormEmpty) {
                    continue;
                }
                $form->setIdTemplate("{0}-$key");
                $form->setBaseCls('x-plain');
                try {
                    $title = Vpc_Abstract::getSetting($component, 'componentName');
                    $fieldset = new Vps_Form_Container_FieldSet($title);
                    $fieldset->add($form);
                    $this->add($fieldset);
                } catch (Vps_Exception $e) {
                    $this->add($form);
                }
            } catch (Vps_Exception $e) {
            }
        }
    }
}
