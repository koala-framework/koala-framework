<?php
class Kwc_FulltextSearch_Search_Directory_MaintenanceJobs_UpdateChanged extends Kwf_Util_Maintenance_Job_Abstract
{
    public function getFrequency()
    {
        return self::FREQUENCY_MINUTELY;
    }

    public function hasWorkload()
    {
        $pagesMetaModel = Kwf_Component_PagesMetaModel::getInstance();
        $s = $pagesMetaModel->select();
        $s->where(new Kwf_Model_Select_Expr_Higher('changed_date', new Kwf_DateTime(time() - 5*60))); //>5min ago (for buffering!)
        $s->whereEquals('fulltext_skip', false);
        $s->where('changed_date > fulltext_indexed_date OR ISNULL(fulltext_indexed_date)');
        return $pagesMetaModel->countRows($s) > 0;
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
