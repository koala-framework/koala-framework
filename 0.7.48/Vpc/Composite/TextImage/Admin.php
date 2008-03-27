<?php
class Vpc_Composite_TextImage_Admin extends Vpc_Abstract_Composite_Admin
{
    public function setup()
    {
        parent::setup();
        $fields['image_position'] = "enum('left', 'right', 'alternate') default NULL";
        $this->createFormTable('vpc_composite_textimage', $fields);
    }
}
