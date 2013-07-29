<?php
class Kwc_FavouritesSelenium_Favourite_Model extends Kwf_Model_Session
{
    public function __construct($config = array())
    {
        $config['columns'] = array('id', 'component_id', 'user_id');
        $config['namespace'] = 'favourites_model_session';
        $config['primaryKey'] = 'id';
        $config['defaultData'] = array(
        );
        parent::__construct($config);
    }
}
