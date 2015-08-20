<?php
class Kwc_Root_MaintenanceJobs_CacheCleanup extends Kwf_Util_Maintenance_Job_Abstract
{
    public function getFrequency()
    {
        return self::FREQUENCY_DAILY;
    }

    public function execute($debug)
    {
        $cmd = "php bootstrap.php component-collect-garbage";
        if ($debug) $cmd .= " --debug";
        if ($debug) echo "$cmd\n";
        passthru($cmd);
    }
}
