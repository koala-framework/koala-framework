<?php
class Vpc_Forum_User_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'childComponentClasses' => array(
                'edit' => 'Vpc_Forum_User_Edit_Component',
                'view' => 'Vpc_Forum_User_View_Component'
            )
        ));
    }
}