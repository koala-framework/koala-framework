<?php
class Kwc_Root_MaintenanceJobs_PageMetaUpdate extends Kwf_Util_Maintenance_Job_Abstract
{
    public function getFrequency()
    {
        return self::FREQUENCY_MINUTELY;
    }

    public function hasWorkload()
    {
        return true;
    }

    public function execute($debug)
    {
        $cmd = "php bootstrap.php component-pages-meta update-changed-job";
        if ($debug) $cmd .= " --debug";
        if ($debug) echo "$cmd\n";
        passthru($cmd);
    }
}
