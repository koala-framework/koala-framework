<?php
/**
 * Wrapper around proc_open
 */
class Vps_Util_Proc
{
    private $_process;
    private $_pipes;

    public function __construct($cmd, $descriptorspec, $cwd = null, $env = null)
    {
        $this->_process = proc_open($cmd, $descriptorspec, $this->_pipes, $cwd, $env);
        if (!is_resource($this->_process)) {
            throw new Vps_Exception("Command failed: $cmd");
        }
    }

    public function pipe($nr)
    {
        return $this->_pipes[$nr];
    }

    public function close()
    {
        $ret = proc_close($this->_process);
        if ($ret) {
            throw new Vps_Exception("Command failed");
        }
    }
}
