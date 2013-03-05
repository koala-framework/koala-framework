<?php
class Kwc_FavouritesSelenium_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        /*
         * root -> 2000 [paragraphs] (Home)
         * root -> 2001 [parentStatic]
         * root -> 2001_normalComponentWithFavourite-favourite
         * root -> 2002 [normalComponentWithFavourite]
         * root -> 2002_favourite
         * root -> 2003_favourite
         * root -> 2004 [favouritesPage]
         * root -> 2005 [favouritesBox]
         */
        $ret['generators']['page']['model'] = 'Kwc_FavouritesSelenium_Root_PagesModel';
        $ret['generators']['page']['component'] = array();
        $ret['generators']['page']['component']['paragraphs'] = 'Kwc_Basic_Empty_Component';
        $ret['generators']['page']['component']['favouritesParentStatic'] = 'Kwc_FavouritesSelenium_ParentStaticFavourite_Component';
        $ret['generators']['page']['component']['favourites'] = 'Kwc_FavouritesSelenium_Favourite_Component';
        $ret['generators']['page']['component']['favouritesBox'] = 'Kwc_Favourites_Box_Component';
        $ret['generators']['page']['component']['favouritesPage'] = 'Kwc_FavouritesSelenium_Page_TestComponent';
        $ret['generators']['page']['component']['selenium'] = 'Kwc_FavouritesSelenium_Selenium_Component';

        unset($ret['generators']['title']);
        return $ret;
    }
}
