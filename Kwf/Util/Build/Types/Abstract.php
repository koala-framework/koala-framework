<?php
abstract class Kwf_Util_Build_Types_Abstract
{    
    const SILENT = 'silent';
    const VERBOSE = 'verbose';
    protected $_verbosity = self::SILENT;
    private $_output = '';
    private $_success = true;

    protected function _build($options) {}

    public final function build($options)
    {
        try {
            $this->_build($options);
        } catch (Exception $e) {
            $this->_success = false;
            $this->_output("$e");
        }
    }

    public function setVerbosity($verbosity)
    {
        $this->_verbosity = $verbosity;
    }

    protected function _output($msg)
    {
        if ($this->_verbosity == self::VERBOSE) {
            echo $msg;
        }
        $this->_output .= $msg;
    }

    public function getOutput()
    {
        return $this->_output;
    }

    public function getSuccess()
    {
        return $this->_success;
    }

    public function checkRequirements()
    {
    }

    abstract public function getTypeName();
}
