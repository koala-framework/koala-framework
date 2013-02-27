<?php
class Kwc_Favourites_Favourite_Model extends Kwf_Model_Session
{
    public function __construct($config = array())
    {
        $config['columns'] = array('id', 'component_id', 'user_id');
        $config['namespace'] = 'favourites_model_session';
        $config['primaryKey'] = 'id';
        $config['defaultData'] = array(
            array('id'=>1, 'component_id'=>'2001_favourite', 'user_id'=>1),
            array('id'=>2, 'component_id'=>'2002', 'user_id'=>1),
            array('id'=>4, 'component_id'=>'2001_favourite', 'user_id'=>2)
        );
        parent::__construct($config);
    }
}
