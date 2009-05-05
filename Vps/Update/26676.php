<?php
class Vps_Update_26676 extends Vps_Update
{
    public function update()
    {
        $svn = array();
        exec('chmod 0777 application/log');
        exec('chmod 0777 application/log/error');
        exec('chmod 0777 application/log/notfound');
        exec('chmod 0777 application/log/accessdenied');
    }
}
