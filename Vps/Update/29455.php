<?php
class Vps_Update_29455 extends Vps_Update
{
    protected $_tags = array('vps');

    public function update()
    {
        if (file_exists('application/cache') && !file_exists('application/cache/benchmark')) {
            mkdir('application/cache/benchmark');
            system('svn add application/cache/benchmark');
            system("svn propset svn:ignore '*' application/cache/benchmark");
        }
    }
}
