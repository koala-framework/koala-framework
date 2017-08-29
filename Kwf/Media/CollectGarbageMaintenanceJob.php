<?php
class Kwf_Media_CollectGarbageMaintenanceJob extends Kwf_Util_Maintenance_Job_Abstract
{
    public function getFrequency()
    {
        return self::FREQUENCY_DAILY;
    }

    public function execute($debug)
    {
        Kwf_Media::collectGarbage($debug);
    }
}
