<?php
class Kwc_Advanced_CommunityVideo_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_ShowField('original_url', trlKwfStatic('Original Url')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('url'));
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwfStatic('Own Url')));
        $fs->setCheckboxToggle(true);
        $fs->setCheckboxName('own_url');
        $fs->add(new Kwf_Form_Field_UrlField('url', trlKwfStatic('URL')))
            ->setAllowBlank(false)
            ->setWidth(400);
    }
}
