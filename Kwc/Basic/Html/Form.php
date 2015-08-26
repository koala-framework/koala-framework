<?php
class Kwc_Basic_Html_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf("Content")));
        $fs->fields->prepend(new Kwf_Form_Field_TextArea('content'))
            ->setFieldLabel(trlKwf('Content'))
            ->setHideLabel(true)
            ->setHeight(225)
            ->setWidth(450)
            ->setAllowTags(true);
    }
}
