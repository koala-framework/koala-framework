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
        $runsModel = Kwf_Model_Abstract::getInstance('Kwf_Util_Maintenance_JobRunsModel');
        $runRow = $runsModel->createRow();
        $runRow->job = get_class($job);
        $runRow->start = date('Y-m-d H:i:s');
        $runRow->last_process_seen = date('Y-m-d H:i:s');
        $runRow->status = 'starting';
        $runRow->save();

        $maxTime = $job->getMaxTime();
        $t = microtime(true);
        $cmd = "php bootstrap.php maintenance-jobs internal-run-job --runId=".escapeshellarg($runRow->id);
        if ($debug) $cmd .= " --debug";

        $process = new Process($cmd);
        $process->start(function ($type, $buffer) use ($runsModel, $runRow) {
            if (Process::ERR === $type) {
                echo $buffer;
            } else {
                echo $buffer;
            }
            Kwf_Registry::get('db')->query("UPDATE {$runsModel->getTableName()} SET log=CONCAT(log, ?) WHERE id=?", array($buffer, $runRow->id));
        });

        $runRow->pid = $process->getPid();
        $runRow->status = 'running';

        $runRow->save();

        while ($process->isRunning()) {
            if (microtime(true)-$t > $maxTime*2) {
                //when jobs runs maxTime twice kill it
                file_put_contents('php://stderr', "\nWARNING: Killing maintenance-jobs process (running > maxTime*2)...\n");
                $process->stop();
                $runRow->status = 'killed';
                break;
            }
            $runRow->runtime = microtime(true)-$t;
            $runRow->last_process_seen = date('Y-m-d H:i:s');
            $runRow->save();
            sleep(1);
        }
        $runRow->runtime = microtime(true)-$t;
        if (!is_null($runRow->progress)) {
            $runRow->progress = 100;
        }

        if ($runRow->status != 'killed') {
            if ($process->getExitCode()) {
                $runRow->status = 'failed';
                $e = new Kwf_Exception("Maintenance job ".get_class($job)." failed with exit code ".$process->getExitCode());
                $e->log();
            } else {
                $runRow->status = 'success';
            }
        }
        $runRow->save();


        $t = microtime(true)-$t;
        if ($debug) echo "executed ".get_class($job)." in ".round($t, 3)."s\n";

        if ($t > $maxTime) {
            $msg = "Maintenance job ".get_class($job)." took ".round($t, 3)."s to execute which is above the limit of $maxTime.";
            file_put_contents('php://stderr', $msg."\n");
            $e = new Kwf_Exception($msg);
            $e->log();
        }
        if ($debug) echo "\n";
    }
}
