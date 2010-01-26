<?php
class Vps_Update_33018 extends Vps_Update
{
    protected $_tags = array('db');

    public function update()
    {
        $dbConfig = Vps_Registry::get('db')->getConfig();
        Vps_Util_Mysql::grantFileRight($dbConfig['username']);
    }
}
