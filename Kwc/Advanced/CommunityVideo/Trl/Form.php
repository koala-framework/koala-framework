<?php
class Kwc_Advanced_CommunityVideo_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $fs = $this->fields->add(new Kwf_Form_Container_FieldSet(trlKwf('Master')));
        $fs->add(new Kwf_Form_Field_ShowField('url_master', trlKwf('Master URL')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('url'));
        $fs->add(new Kwf_Form_Field_ShowField('width_master', trlKwf('Master width')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('width'));
        $fs->add(new Kwf_Form_Field_ShowField('height_master', trlKwf('Master height')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('height'));

        $this->fields->add(new Kwf_Form_Field_Static(trlKwf('Please insert the web address to your YouTube or Vimeo video.')))
            ->setWidth(400);
        $this->fields->add(new Kwf_Form_Field_TextField('url', trlKwf('URL')))
            ->setWidth(400)
            ->setVtype('url');
        $this->fields->add(new Kwf_Form_Field_NumberField('width', trlKwf('Width')))
            ->setMinValue(1)
            ->setMaxValue(9999);
        $this->fields->add(new Kwf_Form_Field_NumberField('height', trlKwf('Height')))
            ->setMinValue(1)
            ->setMaxValue(9999);
        $this->fields->add(new Kwf_Form_Field_Checkbox('show_similar_videos', trlKwf('Show similar videos (YouTube only)')));
    }
}
