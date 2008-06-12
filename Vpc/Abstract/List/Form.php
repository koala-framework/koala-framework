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

        $childComponentClasses = Vpc_Abstract::getSetting($this->getClass(), 'childComponentClasses');
        $childComponentClass = $childComponentClasses['child'];
        $multifields->fields->add(Vpc_Abstract_Form::createComponentForm('child', $childComponentClass))
            ->setIdTemplate('{component_id}-{id}');
        return $multifields;
    }
}