<?php
class Kwc_Favourites_Favourite_Controller extends Kwc_Favourites_Controller
{
    public function preDispatch()
    {
        parent::preDispatch();
        //use custom user model
        Kwf_Registry::get('config')->user->model = 'Kwc_Favourites_UserModel';

        //unset existing userModel instance to get new one
        $reg = Kwf_Registry::getInstance()->set('userModel',
            Kwf_Model_Abstract::getInstance('Kwc_Favourites_UserModel')
        );
    }
}