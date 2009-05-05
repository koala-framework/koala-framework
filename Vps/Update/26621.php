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
            exec("svn add $dir");
            exec("svn propset svn:ignore \"*\" $dir");
            return true;
        }
        return false;
    }
}
