<?php
class Vps_User_UserModel extends Vps_User_Service_Model
{
    protected $_rowClass = 'Vps_User_UserRow';

    public function getWebcode()
    {
        return 'wctest';
    }
}
