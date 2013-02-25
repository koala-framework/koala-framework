<?php
class Kwc_Favourites_Page_TestComponent extends Kwc_Favourites_Page_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['favouritesModel'] = 'Kwc_Favourites_Favourite_Model';
        return $ret;
    }
}
