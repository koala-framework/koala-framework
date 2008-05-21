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
        $multifields->setMinEntries(0);
        $multifields->fields->add(new Vps_Form_Field_Checkbox('visible', trlVps('Visible')));
        return $multifields;
    }
}