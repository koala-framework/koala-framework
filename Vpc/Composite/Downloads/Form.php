<?php
class Vpc_Composite_Downloads_Form extends Vpc_Abstract_List_Form
{
    protected function _getMultiFields()
    {
        $childComponentClasses = Vpc_Abstract::getSetting($this->getClass(), 'childComponentClasses');
        $childComponentClass = $childComponentClasses['child'];
        $form = Vpc_Admin::getComponentFile($childComponentClass, 'Form', 'php', true);
        $multifields = parent::_getMultiFields();
        $multifields->fields->add(new $form($childComponentClass, $childComponentClass));
        
        return $multifields;
    }
}
