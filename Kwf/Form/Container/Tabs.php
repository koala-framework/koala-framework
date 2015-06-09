<?php
/**
 * @package Form
 */
class Kwf_Form_Container_Tabs extends Kwf_Form_Container_Abstract
{
    public function __construct($name = null)
    {
        $this->fields = new Kwf_Collection_FormFields(null, 'Kwf_Form_Container_Tab');
        parent::__construct($name);
        $this->setDeferredRender(false); //verursacht combobox-view-breite-bug
        $this->setBaseCls('x2-plain');
        $this->setXtype('tabpanel');
        $this->setLayout(null);
    }
}
