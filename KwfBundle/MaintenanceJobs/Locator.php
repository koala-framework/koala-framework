<?php
namespace KwfBundle\MaintenanceJobs;

class Locator
{
    private $maintenanceJobServiceIds;

    public function __construct()
    {
        $this->maintenanceJobServiceIds = array();
    }

    public function addMaintenanceJobServiceId($jobServiceId)
    {
        $this->maintenanceJobServiceIds[] = $jobServiceId;
    }

    public function getMaintenanceJobServiceIds()
    {
        return $this->maintenanceJobServiceIds;
    }
}
