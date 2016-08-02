<?php
class Kwc_Root_MaintenanceJobs_PageMetaRebuild extends Kwf_Util_Maintenance_Job_Abstract
{
    public function getFrequency()
    {
        return self::FREQUENCY_DAILY;
    }

    public function execute($debug)
    {
        $cmd = "php bootstrap.php component-pages-meta rebuild";
        if ($debug) $cmd .= " --debug";
        if ($debug) echo "$cmd\n";
        passthru($cmd);
    }

    public function getMaxTime()
    {
        $ret = parent::getMaxTime();
        if ($value = Kwf_Config::getValue('maintenanceJobs.pageMetaRebuildMaxTime')) {
            $ret = $value;
        }
        return $ret;
    }
}
