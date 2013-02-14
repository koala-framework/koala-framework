<?php
class Kwc_Favourites_Model extends Kwf_Model_Db
{
    protected $_table = 'kwc_favourites';

    protected function _init()
    {
        parent::_init();
        $this->_referenceMap['User'] = 'user_id->' . Kwf_Registry::get('config')->user->model;
    }
}
