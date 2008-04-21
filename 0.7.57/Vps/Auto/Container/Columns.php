<?php
class Vps_Auto_Container_Columns extends Vps_Auto_Container_Abstract
{
    public function __construct($name = null)
    {
        $this->fields = new Vps_Collection_FormFields(null, 'Vps_Auto_Container_Column');
        parent::__construct($name);
        $this->setLayout('column');
        $this->setBorder(false);
        $this->setBaseCls('x-plain');
    }
}
