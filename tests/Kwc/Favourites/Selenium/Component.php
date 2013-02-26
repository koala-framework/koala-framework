<?php
class Kwc_Favourites_Selenium_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['favouritesbox'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Favourites_Box_Component',
            'unique' => true,
            'inherit' => true
       );
        $ret['generators']['child']['component']['favourites'] = 'Kwc_Favourites_Favourite_Component';
        $ret['flags']['resetMaster'] = true;
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function preProcessInput()
    {
        //use custom user model
        Kwf_Registry::get('config')->user->model = 'Kwc_Favourites_UserModel';
        //unset existing userModel instance to get new one
        $reg = Kwf_Registry::getInstance()->set('userModel',
            Kwf_Model_Abstract::getInstance('Kwc_Favourites_UserModel')
        );
    }
}
