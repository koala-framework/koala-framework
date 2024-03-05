<?php
class Kwc_Root_MaintenanceJobs_DeleteOldEnquiries extends Kwf_Util_Maintenance_Job_Abstract
{
    public function getFrequency()
    {
        return self::FREQUENCY_DAILY;
    }

    public function execute($debug)
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Model_Mail');
        $deleteAfterDays = Kwf_Config::getValue('enquiries.deleteAfterDays');

        if ($deleteAfterDays) {
            $deleteBeforeDate = new Kwf_Date("-{$deleteAfterDays}days");
            $select = new Kwf_Model_Select();
            $select->where(new Kwf_Model_Select_Expr_Lower('save_date', $deleteBeforeDate));
            $model->deleteRows($select);
        }
    }
}
