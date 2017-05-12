<?php
class Kwc_Root_MaintenanceJobs_PageMetaUpdate extends Kwf_Util_Maintenance_Job_Abstract
{
    public function getFrequency()
    {
        return self::FREQUENCY_MINUTELY;
    }

    public function hasWorkload()
    {
        $m = Kwf_Component_PagesMetaModel::getInstance();
        $s = $m->select();
        $s->whereEquals('changed_recursive', true);
        return $m->countRows($s) > 0;
    }

    public function execute($debug)
    {
        $cmd = "php bootstrap.php component-pages-meta update-changed-job";
        if ($debug) $cmd .= " --debug";
        if ($debug) echo "$cmd\n";
        passthru($cmd, $retVal);
        if ($retVal) throw new Kwf_Exception("Process failed");
    }
}
