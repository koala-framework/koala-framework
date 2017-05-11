<?php
use \Symfony\Component\Process\Process;

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
        foreach (Kwf_Model_Abstract::findAllInstances() as $model) {
            if ($model instanceof Kwf_Util_Maintenance_JobProviderInterface) {
                $providerClasses[] = get_class($model);;
            }
        }

        $jobClasses = array();
        foreach ($providerClasses as $c) {
            $jobClasses = array_merge($jobClasses, call_user_func(array($c, 'getMaintenanceJobs')));
        }
        $jobClasses = array_unique($jobClasses);
        $ret = array();
        foreach ($jobClasses as $i) {
            $ret[] = new $i();
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

    public static function executeJobs($jobFrequency, $debug)
    {
        foreach (self::getAllMaintenanceJobs() as $job) {
            if ($job->getFrequency() == $jobFrequency) {
                if (!$job->hasWorkload()) continue;
                if ($debug) echo "executing ".get_class($job)."\n";
                self::executeJob($job, $debug);
            }
        }
    }

    public static function executeJob($job, $debug)
    {
        $maxTime = $job->getMaxTime();
        $t = microtime(true);
        $cmd = "php bootstrap.php maintenance-jobs internal-run-job --job=".escapeshellarg(get_class($job));
        if ($debug) $cmd .= " --debug";

        $process = new Process($cmd);
        $process->start(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo $buffer;
            } else {
                echo $buffer;
            }
        });

        while ($process->isRunning()) {
            if (microtime(true)-$t > $maxTime*2) {
                //when jobs runs maxTime twice kill it
                file_put_contents('php://stderr', "\nWARNING: Killing maintenance-jobs process (running > maxTime*2)...\n");
                $process->stop();
                break;
            }
            sleep(1);
        }
        if ($process->getExitCode()) {
            $e = new Kwf_Exception("Maintenance job ".get_class($job)." failed with exit code ".$process->getExitCode());
            $e->logOrThrow();
        }
        $t = microtime(true)-$t;
        if ($debug) echo "executed ".get_class($job)." in ".round($t, 3)."s\n";

        if ($t > $maxTime) {
            $msg = "Maintenance job ".get_class($job)." took ".round($t, 3)."s to execute which is above the limit of $maxTime.";
            file_put_contents('php://stderr', $msg."\n");
            $e = new Kwf_Exception($msg);
            $e->logOrThrow();
        }
        if ($debug) echo "\n";
    }
}
