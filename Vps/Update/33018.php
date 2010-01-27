<?php
class Vps_Update_33018 extends Vps_Update
{
    protected $_tags = array('vps');

    public function update()
    {
        $dbConfig = Vps_Registry::get('db')->getConfig();
        if ($dbConfig && !empty($dbConfig['username'])) {
            Vps_Util_Mysql::grantFileRight($dbConfig['username']);
        }
    }
}
