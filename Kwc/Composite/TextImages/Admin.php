<?php
class Kwc_Composite_TextImages_Admin extends Kwc_Abstract_Composite_Admin
{
    public function getExtConfig()
    {
        $config = parent::getExtConfig();
        $config['tabs']['Properties'] = Kwc_Abstract_Composite_Admin::getExtConfig();
        return $config;
    }
}
