<?php
class Kwf_Util_Maintenance_Dispatcher
{
    public static function getAllMaintenanceJobs()
    {
        static $ret;
        if (isset($ret)) return $ret;

        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (is_instance_of($c, 'Kwf_Util_Maintenance_JobProviderInterface')) {
                $providerClasses[] = $c;
            }
        }

        $ret = array();
        foreach ($providerClasses as $c) {
            $ret = array_merge($ret, call_user_func(array($c, 'getMaintenanceJobs')));
        }
        usort($ret, array('Kwf_Util_Maintenance_Dispatcher', '_compareJobsPriority'));
        return $ret;
    }

    public static function _compareJobsPriority($a, $b)
    {
        $a = $a->getPriority();
        $b = $b->getPriority();
        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
    }

    public static function executeMinutely($debug)
    {
        foreach (self::getAllMaintenanceJobs() as $job) {
            if ($job->getFrequency() == Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_MINUTELY) {
                if ($debug) echo "\nexecuting ".get_class($job)."\n";
                $job->execute($debug);
            }
        }
    }

    public static function executeDaily($debug)
    {
        foreach (self::getAllMaintenanceJobs() as $job) {
            if ($job->getFrequency() == Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_DAILY) {
                if ($debug) echo "\nexecuting ".get_class($job)."\n";
                $t = microtime(true);
                $job->execute($debug);
                if ($debug) echo "\nexecuted ".get_class($job)." in ".round(microtime(true)-$t, 3)."s\n";
            }
        }
    }
}
