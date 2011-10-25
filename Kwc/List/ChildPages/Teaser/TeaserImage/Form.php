<?php
class Kwc_List_ChildPages_Teaser_TeaserImage_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        $fs = $this->fields->add(new Kwf_Form_Container_FieldSet(trlKwf('Status')));
        $fs->add(new Kwf_Form_Field_Checkbox('visible'))
            ->setFieldLabel(trlKwf('Visible'));
        $fs->add(new Kwf_Form_Field_TextField('link_text'))
            ->setFieldLabel(trlKwf('Link text'))
            ->setWidth(300);
        parent::_initFields();
    }
}
