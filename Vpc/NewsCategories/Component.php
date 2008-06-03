<?php
class Vpc_NewsCategories_Component extends Vpc_News_Directory_Component
{

    public static function getSettings()
    {
        $settings = parent::getSettings();
//         $settings['childComponentClasses']['categories'] ='Vpc_News_Category_Directory_Component';
//         $settings['childComponentClasses']['months'] = 'Vpc_News_Month_Directory_Component';
        return $settings;
    }

}
