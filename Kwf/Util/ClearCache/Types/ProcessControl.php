<?php
class Kwf_Util_ClearCache_Types_ProcessControl extends Kwf_Util_ClearCache_Types_Abstract
{
    public function getTypeName()
    {
        return 'processControl';
    }

    public function doesRefresh()
    {
        return true;
    }
    public function doesClear()
    {
        return false;
    }

    protected function _refreshCache($options)
    {
        $cmd = Kwf_Config::getValue('server.phpCli').' bootstrap.php process-control restart --silent 2>&1';
        exec($cmd, $out, $ret);
        if ($ret) {
            throw new Kwf_Exception("restart failed:\n".implode("\n", $out));
        } else {
            $this->_output(implode("\n", $out));
        }
    }
}
