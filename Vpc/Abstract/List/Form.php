<?php
class Vpc_Abstract_List_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $this->setProperty('class', $class);
        $this->add($this->_getMultiFields());
    }
    
    protected function _getMultiFields()
    {
        $multifields = new Vps_Form_Field_MultiFields('Children');
        $multifields->setMinEntries(0);
        $multifields->fields->add(new Vps_Form_Field_Checkbox('visible', trlVps('Visible')));
        $multifields->setPosition(true);

        $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), 'child');
        $form->setIdTemplate('{component_id}-{id}');
        $multifields->fields->add($form);
        
        return $multifields;
    }
    
}