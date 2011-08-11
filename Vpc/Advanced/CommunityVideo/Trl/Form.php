<?php
class Vpc_Advanced_CommunityVideo_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $fs = $this->fields->add(new Vps_Form_Container_FieldSet(trlVps('Master')));
        $fs->add(new Vps_Form_Field_ShowField('url_master', trlVps('Master URL')))
            ->setData(new Vps_Data_Trl_OriginalComponent('url'));
        $fs->add(new Vps_Form_Field_ShowField('width_master', trlVps('Master width')))
            ->setData(new Vps_Data_Trl_OriginalComponent('width'));
        $fs->add(new Vps_Form_Field_ShowField('height_master', trlVps('Master height')))
            ->setData(new Vps_Data_Trl_OriginalComponent('height'));

        $this->fields->add(new Vps_Form_Field_Static(trlVps('Please insert the web address to your YouTube or Vimeo video.')))
            ->setWidth(400);
        $this->fields->add(new Vps_Form_Field_TextField('url', trlVps('URL')))
            ->setWidth(400)
            ->setVtype('url');
        $this->fields->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
            ->setMinValue(1)
            ->setMaxValue(9999);
        $this->fields->add(new Vps_Form_Field_NumberField('height', trlVps('Height')))
            ->setMinValue(1)
            ->setMaxValue(9999);
        $this->fields->add(new Vps_Form_Field_Checkbox('show_similar_videos', trlVps('Show similar videos (YouTube only)')));
    }
}
