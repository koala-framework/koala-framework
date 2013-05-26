<?php
abstract class Kwf_Util_ClearCache_Types_Abstract
{    
    const SILENT = 'silent';
    const VERBOSE = 'verbose';
    protected $_verbosity = self::SILENT;
    private $_output = '';
    private $_success = true;

    protected function _clearCache($options) {}
    protected function _refreshCache($options) {}

    public final function clearCache($options)
    {
        try {
            $this->_clearCache($options);
        } catch (Exception $e) {
            $this->_success = false;
            $this->_output("$e");
        }
    }

    public final function refreshCache($options)
    {
        try {
            $this->_refreshCache($options);
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

    abstract public function getTypeName();
    abstract public function doesClear();
    abstract public function doesRefresh();
}
