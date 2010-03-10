<?php
class Vps_Update_26621 extends Vps_Update
{
    public function update()
    {
        $svn = array();
        $this->_addDir('application/log');
        $this->_addDir('application/log/error');
        $this->_addDir('application/log/notfound');
        $this->_addDir('application/log/accessdenied');
    }

    private function _addDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
            if (file_exists('.svn')) {
                exec("svn add $dir");
                exec("svn propset svn:ignore \"*\" $dir");
            } else {
                file_put_contents($dir.'.gitignore', '*');
                exec("git add $dir");
            }
        }
    }
}
