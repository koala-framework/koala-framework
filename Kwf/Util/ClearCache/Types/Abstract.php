<?php
abstract class Kwf_Util_ClearCache_Types_Abstract
{    
    const SILENT = 'silent';
    const VERBOSE = 'verbose';
    protected $_verbosity = self::SILENT;

    public function clearCache($options)
    {
    }

    public function refreshCache($options)
    {
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
    }

    abstract public function getTypeName();
    abstract public function doesClear();
    abstract public function doesRefresh();
}
