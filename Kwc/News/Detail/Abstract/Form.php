<?php
class Kwc_News_Detail_Abstract_Form extends Kwc_Directories_Item_Detail_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
            ->setAllowBlank(false)
            ->setWidth(300);
        $this->add(new Kwf_Form_Field_TextArea('teaser', trlKwf('Teaser')))
            ->setWidth(300)
            ->setHeight(100);
        $this->add(new Kwf_Form_Field_DateField('publish_date', trlKwf('Publish Date')))
            ->setAllowBlank(false);
        if (Kwc_Abstract::getSetting($this->getDirectoryClass(), 'enableExpireDate')) {
            $this->add(new Kwf_Form_Field_DateField('expiry_date', trlKwf('Expiry Date')));
        }
    }
}
