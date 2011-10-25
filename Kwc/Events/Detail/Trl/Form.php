<?php
class Kwc_Events_Detail_Trl_Form extends Kwf_Form
{
    public function __construct($directoryClass = null)
    {
        $this->setDirectoryClass($directoryClass);
        parent::__construct('details');
    }

    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
            ->setAllowBlank(false)
            ->setWidth(300);
        $this->add(new Kwf_Form_Field_ShowField('original_title', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponentFromData('title'));

        $this->add(new Kwf_Form_Field_TextField('place', trlKwf('Place (City)')))
            ->setWidth(300);
        $this->add(new Kwf_Form_Field_ShowField('original_place', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponentFromData('place'));

        $this->add(new Kwf_Form_Field_TextArea('teaser', trlKwf('Teaser')))
            ->setWidth(300)
            ->setHeight(100);
        $this->add(new Kwf_Form_Field_ShowField('original_teaser', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponentFromData('teaser'));
    }
}
