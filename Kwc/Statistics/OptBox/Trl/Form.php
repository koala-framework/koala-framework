<?php
class Kwc_Statistics_OptBox_Trl_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextField('headline', trlKwf('Headline')))
            ->setAllowBlank(true)
            ->setWidth(500);

        $this->fields->add(new Kwf_Form_Field_ShowField('original_headline', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('headline');

        $this->add(new Kwf_Form_Field_TextField('text', trlKwf('Text')))
            ->setDefaultValue(trlKwf('This website uses cookies to help us give you the best experience when you visit our website.'))
            ->setWidth(500);

        $this->fields->add(new Kwf_Form_Field_ShowField('original_text', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('text');

        $this->add(new Kwf_Form_Field_TextField('accept_text', trlKwf('Accept Text')))
            ->setDefaultValue(trlKwf('Accept and continue'))
            ->setWidth(500);

        $this->fields->add(new Kwf_Form_Field_ShowField('original_accept_text', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('accept_text');

        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('More Info')));

        $fs->add(new Kwf_Form_Field_TextField('more_text', trlKwf('More Info Text')))
            ->setDefaultValue(trlKwf('More information about the use of cookies'))
            ->setWidth(500);

        $fs->fields->add(new Kwf_Form_Field_ShowField('original_more_text', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('more_text');

        $fs->add($this->createChildComponentForm($this->getClass(), 'linktag'));
    }
}
