<?php
class Vps_Update_26621 extends Vps_Update
{
    public function update()
    {
        $svn = array();
        $dir = 'application/log';
        if ($this->_addDir($dir)) $svn[] = $dir;

        $dir = 'application/log/error';
        if ($this->_addDir($dir)) $svn[] = $dir;

        $dir = 'application/log/notfound';
        if ($this->_addDir($dir)) $svn[] = $dir;

        $dir = 'application/log/accessdenied';
        if ($this->_addDir($dir)) $svn[] = $dir;

        if (!empty($svn)) {
            $dirs = implode(' ', $svn);
            exec("svn ci $dirs -m 'Error-Verzeichnisse hinzugefuegt.'");
        }
    }

    private function _addDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir);
            exec("svn add $dir");
            exec("svn propset svn:ignore \"*\" $dir");
            return true;
        }
        return false;
    }
}
