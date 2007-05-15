<?php

class Vps_User_Users extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';
    protected $_primary = 'id';
    protected $_dependentTables = array('CustomerComments');
    protected $_rowClass = 'User';
}
