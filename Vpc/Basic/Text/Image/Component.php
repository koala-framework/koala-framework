<?php
class Vpc_Basic_Text_Image_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(),
            array('allowBlank' => false,
                  'dimension'  => array()
            ));
    }
}
