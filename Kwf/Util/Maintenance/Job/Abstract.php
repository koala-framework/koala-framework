<?php
abstract class Kwf_Util_Maintenance_Job_Abstract
{
    const FREQUENCY_DAILY = 'daily';
    const FREQUENCY_HOURLY = 'hourly';
    const FREQUENCY_MINUTELY = 'minutely';
    const FREQUENCY_SECONDS = 'seconds';
    const FREQUENCY_CUSTOM = 'custom';

    protected $_debug = false;

    protected $_progressBar;
    protected $_jobRun;

    abstract public function getFrequency();

    public function getPriority()
    {
        return 0;
    }

    public function getMaxTime()
    {
        $ret = 60;
        if ($this->getFrequency() == self::FREQUENCY_DAILY) {
            $ret = 60 * 60;
        }
        return $ret;
    }

    public function setDebug($debug)
    {
        $this->_debug = $debug;
    }

    protected function _log($msg)
    {
        if ($this->_debug) echo $msg . PHP_EOL;
    }

    public function getProgressSteps()
    {
        return null;
    }

    public function setProgressBar(Zend_ProgressBar $progressBar)
    {
        $this->_progressBar = $progressBar;
    }

    public function setJobRun(Kwf_Model_Row_Interface $jobRun)
    {
        $this->_jobRun = $jobRun;
    }

    abstract public function execute($debug);

    public function hasWorkload()
    {
        if ($this->getFrequency() == self::FREQUENCY_SECONDS || $this->getFrequency() == self::FREQUENCY_MINUTELY) {
            throw new Kwf_Exception("hasWorkload has to be implemented for this frequency in job ".get_class($this));
        }
        return true;
    }

    public function getRecipientsForFailNotification()
    {
        return null;
    }

    public function getNextRuntime($lastRun = null)
    {
        if ($this->getFrequency() == self::FREQUENCY_CUSTOM) {
            throw new Kwf_Exception("getNextRuntime has to be implemented for this frequency in job ".get_class($this));
        }
    }
}
