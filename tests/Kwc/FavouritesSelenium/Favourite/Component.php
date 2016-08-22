<?php
class Kwc_FavouritesSelenium_Favourite_Component extends Kwc_Favourites_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['favouritesModel'] = 'Kwc_FavouritesSelenium_Favourite_Model';
        return $ret;
    }
}
