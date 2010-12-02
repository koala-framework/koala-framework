<?php
class Vps_Update_33033 extends Vps_Update
{
    public function update()
    {
        $g = new Vps_Util_Git(Vps_Registry::get('config')->libraryPath);
        $g->pull();
    }
}
