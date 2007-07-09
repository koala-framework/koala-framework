<?php

class Vps_Model_User_Users extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';
    protected $_primary = 'id';
    protected $_rowClass = 'Vps_Model_User_User';
}
