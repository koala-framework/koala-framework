<?php
namespace KwfBundle\MaintenanceJobs;

class Locator
{
    private $maintenanceJobs;

    public function __construct()
    {
        $this->maintenanceJobs = array();
    }

    public function addMaintenanceJob(AbstractJob $job)
    {
        $this->maintenanceJobs[] = $job;
    }

    public function getMaintenanceJobs()
    {
        return $this->maintenanceJobs;
    }
}
