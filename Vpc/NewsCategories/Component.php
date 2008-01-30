<?php
class Vpc_NewsCategories_Component extends Vpc_News_Component
{

    public static function getSettings()
    {
        $settings = parent::getSettings();
        $settings['childComponentClasses']['cat'] = 'Vpc_News_Categories_Component';
        $settings['childComponentClasses']['months'] = 'Vpc_News_Months_Component';

        if (!isset($settings['categories']) || !is_array($settings['categories'])) {
            $settings['categories'] = array();
        }
        $settings['categories']['cat'] = array('pageFactory' => 'Vpc_News_PageFactoryCategories');
        $settings['categories']['months'] = array('pageFactory' => 'Vpc_News_PageFactoryMonths');

        return $settings;
    }

}
