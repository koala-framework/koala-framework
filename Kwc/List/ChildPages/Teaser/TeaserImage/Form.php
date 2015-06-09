<?php
class Kwc_List_ChildPages_Teaser_TeaserImage_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextField('link_text'))
            ->setFieldLabel(trlKwf('Link text'))
            ->setWidth(300);
        parent::_initFields();
    }
}
