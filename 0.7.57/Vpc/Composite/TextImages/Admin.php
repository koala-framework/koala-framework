<?php
class Vpc_Composite_TextImages_Admin extends Vpc_Abstract_Composite_TabsAdmin
{
    public function getExtConfig()
    {
        $config = parent::getExtConfig();
        $config['tabs']['Properties'] = Vpc_Abstract_Composite_Admin::getExtConfig();
        return $config;
    }

    public function setup()
    {
        parent::setup();
        $fields['image_position'] = "enum('left', 'right', 'alternate') default NULL";
        $this->createFormTable('vpc_composite_textimages', $fields);
    }
}
