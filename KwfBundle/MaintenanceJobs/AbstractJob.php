<?php
namespace KwfBundle\MaintenanceJobs;

use Psr\Log\LoggerInterface;

abstract class AbstractJob extends \Kwf_Util_Maintenance_Job_AbstractBase
{
    abstract public function execute(LoggerInterface $logger);
}
