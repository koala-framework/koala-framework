<?php
class Kwc_Blog_Detail_Form extends Kwc_Directories_Item_Detail_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
            ->setAllowBlank(false)
            ->setWidth(300);
        $this->add(new Kwf_Form_Field_DateField('publish_date', trlKwf('Publish Date')))
            ->setAllowBlank(false);
    }
}
