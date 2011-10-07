<?php
class Kwc_Basic_Link_Form extends Kwc_Abstract_Composite_Form
{
    protected $_createFieldsets = false;
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextField('text', trlKwf('Linktext')))
            ->setWidth(300);

        parent::_initFields();
    }
}
