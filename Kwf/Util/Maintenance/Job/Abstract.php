<?php
abstract class Kwf_Util_Maintenance_Job_Abstract
{
    const FREQUENCY_DAILY = 'daily';
    const FREQUENCY_MINUTELY = 'minutely';
    const FREQUENCY_SECONDS = 'seconds';

    abstract public function getFrequency();

    public function getPriority()
    {
        return 0;
    }

    abstract public function execute($debug);
}
