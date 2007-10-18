<?php
class Vps_Auto_Container_FieldSet extends Vps_Auto_Container_Abstract
{
    public function __construct($title = null)
    {
        parent::__construct();
        $this->setTitle($title);
        $this->setAutoHeight(true);
        $this->setXtype('fieldset');
    }

//     public function setCheckboxName($name)
//     {
//         $this->fields->add(new Vps_Auto_Field_FieldSet_Checkbox($name));
//         return $this->setProperty('checkboxName', $name);
//     }
}
