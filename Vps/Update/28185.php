<?php
class Vps_Update_28185 extends Vps_Update
{
    public function update()
    {
        if (!file_exists('application/temp')) {
            mkdir('application/temp');
            system('svn add application/temp');
            system("svn propset svn:ignore '*' application/temp");
        }
    }
}
