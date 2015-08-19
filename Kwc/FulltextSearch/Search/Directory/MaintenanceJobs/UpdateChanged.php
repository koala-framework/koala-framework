<?php
class Kwc_FulltextSearch_Search_Directory_MaintenanceJobs_UpdateChanged extends Kwf_Util_Maintenance_Job_Abstract
{
    public function getFrequency()
    {
        return self::FREQUENCY_MINUTELY;
    }

    public function getPriority()
    {
        return 10; //after page meta
    }

    public function execute($debug)
    {
        $cmd = "php bootstrap.php fulltext update-changed-job";
        if ($debug) $cmd .= " --debug";
        if ($debug) echo "$cmd\n";
        passthru($cmd);
    }
}
