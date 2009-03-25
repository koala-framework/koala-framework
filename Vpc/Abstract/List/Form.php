<?php
class Vpc_Abstract_List_Form extends Vps_Form_NonTableForm
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $this->setProperty('class', $class);
        $this->add($this->_getMultiFields());
    }
    
    protected function _getMultiFields()
    {
        $multifields = new Vpc_Abstract_Field_MultiFields($this->getClass());
        $multifields->setReferences(array(
            'columns' => array('component_id'),
            'refColumns' => array('id')
        ));
        $multifields->setMinEntries(0);
        if (Vpc_Abstract::getSetting($this->getClass(), 'showVisible')) {
            $multifields->fields->add(new Vps_Form_Field_Checkbox('visible', trlVps('Visible')));
        }
        $multifields->setPosition(Vpc_Abstract::getSetting($this->getClass(), 'showPosition'));

        $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), 'child');
        $form->setIdTemplate('{component_id}-{id}');
        $multifields->fields->add($form);
        
        return $multifields;
    }
    
}