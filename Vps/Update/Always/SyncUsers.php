<?php
class Vps_Update_Always_SyncUsers extends Vps_Update
{
    public function postClearCache()
    {
        Vps_Registry::get('userModel')->synchronize(true);
    }
}
