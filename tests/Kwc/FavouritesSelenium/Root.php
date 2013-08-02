<?php
class Kwc_FavouritesSelenium_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        /*
         * root -> 2004 [favouritesPage]
         * root -> 2005 [favouritesBox]
         */
        $ret['generators']['page']['model'] = 'Kwc_FavouritesSelenium_Root_PagesModel';
        $ret['generators']['page']['component'] = array();
        $ret['generators']['page']['component']['favouritesBox'] = 'Kwc_Favourites_Box_Component';
        $ret['generators']['page']['component']['favouritesPage'] = 'Kwc_FavouritesSelenium_Page_TestComponent';
        $ret['generators']['page']['component']['selenium'] = 'Kwc_FavouritesSelenium_Selenium_Component';

        unset($ret['generators']['title']);
        return $ret;
    }
}
