<?php
class Kwc_FavouritesSelenium_Selenium_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['favouritesbox'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Favourites_Box_Component',
            'unique' => true,
            'inherit' => true
       );
        $ret['generators']['child']['component']['favourites'] = 'Kwc_FavouritesSelenium_Favourite_Component';
        $ret['flags']['resetMaster'] = true;
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function preProcessInput()
    {
        //use custom user model
        Kwf_Registry::get('config')->user->model = 'Kwc_FavouritesSelenium_UserModel';
        //unset existing userModel instance to get new one
        $reg = Kwf_Registry::getInstance()->set('userModel',
            Kwf_Model_Abstract::getInstance('Kwc_FavouritesSelenium_UserModel')
        );
    }
}
