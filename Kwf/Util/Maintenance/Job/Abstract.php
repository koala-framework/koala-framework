<?php
abstract class Kwf_Util_Maintenance_Job_Abstract extends Kwf_Util_Maintenance_Job_AbstractBase
{
    abstract public function execute($debug);
}
