<?php
class Vps_Update_28185 extends Vps_Update
{
    protected $_tags = array('vps');

    public function update()
    {
        if (!file_exists('application/temp')) {
            mkdir('application/temp');
            if (file_exists('.svn')) {
                system('svn add application/temp');
                system("svn propset svn:ignore '*' application/temp");
            } else {
                file_put_contents('application/temp/.gitignore', '*');
                exec("git add application/temp");
            }
        }
    }
}
