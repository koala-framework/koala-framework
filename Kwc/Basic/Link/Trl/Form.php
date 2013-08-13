<?php
class Kwc_Basic_Link_Trl_Form extends Kwc_Abstract_Composite_Form
{
    protected $_createFieldsets = false;
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextField('text', trlKwf('Linktext')))
            ->setWidth(300);

        $this->add(new Kwf_Form_Field_ShowField('original_text', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('text'));

        parent::_initFields();
    }
}
