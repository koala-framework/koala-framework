<?php
class Vps_Update_Always_SyncUsers extends Vps_Update
{
    public function postClearCache()
    {
        echo "\nbitte warten, das kann je nach Anzahl der Benutzer eine weile dauern...\n";
        if (!Vps_Registry::get('db')) return;
        $tables = Vps_Registry::get('db')->fetchCol('SHOW TABLES');
        if (!in_array('vps_users', $tables)) return;
        if (!in_array('cache_users', $tables)) return;

        Vps_Registry::get('userModel')->synchronize(true);
    }
}
