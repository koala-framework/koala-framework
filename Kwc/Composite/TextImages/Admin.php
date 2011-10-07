<?php
class Kwc_Composite_TextImages_Admin extends Kwc_Abstract_Composite_Admin
{
    public function getExtConfig()
    {
        $config = parent::getExtConfig();
        $config['tabs']['Properties'] = Kwc_Abstract_Composite_Admin::getExtConfig();
        return $config;
    }

    public function setup()
    {
        parent::setup();
        $fields['image_position'] = "enum('left', 'right', 'alternate') default NULL";
        $this->createFormTable('kwc_composite_textimages', $fields);
    }
}
