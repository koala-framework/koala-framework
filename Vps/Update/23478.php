<?php
class Vps_Update_23478 extends Vps_Update
{
    public function update()
    {
        parent::update();
        $str = file_get_contents('application/config.ini');
        $str = str_replace('userModel = ', 'user.model = ', $str);
        file_put_contents('application/config.ini', $str);
    }
}
