<?php
class Kwc_Abstract_List_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $this->setProperty('class', $class);
        $this->add($this->_getMultiFields());
    }
    
    protected function _getMultiFields()
    {
        $multifields = new Kwf_Form_Field_MultiFields('Children');
        $multifields->setMinEntries(0);
        if (Kwc_Abstract::getSetting($this->getClass(), 'hasVisible')) {
            $multifields->fields->add(new Kwf_Form_Field_Checkbox('visible', trlKwf('Visible')));
        }
        $multifields->setPosition(true);

        $form = Kwc_Abstract_Form::createChildComponentForm($this->getClass(), 'child');
        $form->setIdTemplate('{component_id}-{id}');
        $multifields->fields->add($form);
        
        return $multifields;
    }
    
}