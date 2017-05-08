<?php
abstract class Kwf_Util_Maintenance_Job_Abstract
{
    const FREQUENCY_DAILY = 'daily';
    const FREQUENCY_HOURLY = 'hourly';
    const FREQUENCY_MINUTELY = 'minutely';
    const FREQUENCY_SECONDS = 'seconds';

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

    public function getShortName()
    {
        return null;
    }

    abstract public function execute($debug);
}
