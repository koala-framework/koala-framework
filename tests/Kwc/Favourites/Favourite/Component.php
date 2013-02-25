<?php
class Kwc_Favourites_Favourite_Component extends Kwc_Favourites_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['favouritesModel'] = 'Kwc_Favourites_Favourite_Model';
        return $ret;
    }
}
