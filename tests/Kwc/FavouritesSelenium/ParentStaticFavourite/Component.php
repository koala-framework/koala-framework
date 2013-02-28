<?php
class Kwc_FavouritesSelenium_ParentStaticFavourite_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['generators']['favourite'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_FavouritesSelenium_Favourite_Component',
            'name' => 'Favourites'
        );
        return $ret;
    }
}
