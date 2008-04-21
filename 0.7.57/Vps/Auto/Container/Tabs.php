<?php
class Vps_Auto_Container_Tabs extends Vps_Auto_Container_Abstract
{
    public function __construct($name = null)
    {
        $this->fields = new Vps_Collection_FormFields(null, 'Vps_Auto_Container_Tab');
        parent::__construct($name);
        $this->setDeferredRender(false); //verursacht combobox-view-breite-bug
        $this->setBaseCls('x-plain');
        $this->setXtype('tabpanel');
        $this->setLayout(null);
    }
}
