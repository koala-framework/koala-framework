<?php
class Vpc_Events_Detail_Trl_Form extends Vps_Form
{
    public function __construct($directoryClass = null)
    {
        $this->setDirectoryClass($directoryClass);
        parent::__construct('details');
    }

    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Vps_Form_Field_TextField('title', trlVps('Title')))
            ->setAllowBlank(false)
            ->setWidth(300);
        $this->add(new Vps_Form_Field_ShowField('original_title', trlVps('Original')))
            ->setData(new Vps_Data_Trl_OriginalComponentFromData('title'));

        $this->add(new Vps_Form_Field_TextField('place', trlVps('Place (City)')))
            ->setWidth(300);
        $this->add(new Vps_Form_Field_ShowField('original_place', trlVps('Original')))
            ->setData(new Vps_Data_Trl_OriginalComponentFromData('place'));

        $this->add(new Vps_Form_Field_TextArea('teaser', trlVps('Teaser')))
            ->setWidth(300)
            ->setHeight(100);
        $this->add(new Vps_Form_Field_ShowField('original_teaser', trlVps('Original')))
            ->setData(new Vps_Data_Trl_OriginalComponentFromData('teaser'));
    }
}
