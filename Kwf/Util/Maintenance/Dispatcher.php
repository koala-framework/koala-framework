<?php
use \Symfony\Component\Process\Process;

class Kwf_Util_Maintenance_Dispatcher
{
    public static function getAllMaintenanceJobIdentifiers()
    {
        static $ret;
        if (isset($ret)) return $ret;

        $ret = array();
        if ($kernel = Kwf_Util_Symfony::getKernel()) {
            $ret = array_merge($ret, $kernel->getContainer()->get('kwf.maintenance_jobs_locator')->getMaintenanceJobServiceIds());
        }

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
        foreach ($jobClasses as $i) {
            $ret[] = "class:{$i}";
        }

        usort($ret, array('Kwf_Util_Maintenance_Dispatcher', '_compareJobsPriority'));
        return $ret;
    }

    public static function _compareJobsPriority($a, $b)
    {
        $a = Kwf_Util_Maintenance_Job_AbstractBase::getInstance($a)->getPriority();
        $b = Kwf_Util_Maintenance_Job_AbstractBase::getInstance($b)->getPriority();
        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
    }

    public static function executeJobs($jobFrequency, $debug, $output)
    {
        foreach (self::getAllMaintenanceJobIdentifiers() as $jobIdentifier) {
            $job = Kwf_Util_Maintenance_Job_AbstractBase::getInstance($jobIdentifier);

            if ($job->getFrequency() == $jobFrequency) {
                if (!$job->hasWorkload()) continue;
                if ($debug) echo "executing ".get_class($job)."\n";
                self::executeJob($jobIdentifier, $debug, $output);
            }
        }
    }

    public static function executeJob($jobIdentifier, $debug, $output)
    {
        $runsModel = Kwf_Model_Abstract::getInstance('Kwf_Util_Maintenance_JobRunsModel');
        $runRow = $runsModel->createRow();
        $runRow->job = $jobIdentifier;
        $runRow->start = date('Y-m-d H:i:s');
        $runRow->last_process_seen = date('Y-m-d H:i:s');
        $runRow->status = 'starting';
        $runRow->save();

        $job = Kwf_Util_Maintenance_Job_AbstractBase::getInstance($jobIdentifier);
        $maxTime = $job->getMaxTime();
        $t = microtime(true);
        $cmd = "php bootstrap.php maintenance-jobs internal-run-job --runId=".escapeshellarg($runRow->id);
        if ($debug) $cmd .= " --debug";

        $process = new Process($cmd);
        $process->start(function ($type, $buffer) use ($runsModel, $runRow, $output) {
            if ($output) {
                if (Process::ERR === $type) {
                    echo $buffer;
                } else {
                    echo $buffer;
                }
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
                $e = new Kwf_Exception("Maintenance job ".get_class($job)." failed with exit code ".$process->getExitCode()." and ErrorMessage: ".$process->getErrorOutput());
                $e->log();
            } else {
                $runRow->status = 'success';
            }
        }

        $t = microtime(true)-$t;
        if ($debug && $output) echo "executed ".get_class($job)." in ".round($t, 3)."s\n";

        if ($runRow->status == 'success' && $runRow->runtime > $maxTime) {
            $runRow->status = 'timelimit';
            $msg = "Warning: timelimit of $maxTime exceeded (but run successfully)\n";
            Kwf_Registry::get('db')->query("UPDATE {$runsModel->getTableName()} SET log=CONCAT(log, ?) WHERE id=?", array($msg, $runRow->id));
            if ($debug) {
                $msg = "Maintenance job ".get_class($job)." took ".round($t, 3)."s to execute which is above the limit of $maxTime.";
                file_put_contents('php://stderr', $msg."\n");
            }
        }
        if ($debug && $output) echo "\n";

        $runRow->save();
        if ($runRow->status != 'success' && Kwf_Config::getValue('maintenanceJobs.sendFailNotification')) {
            $recipients = $job->getRecipientsForFailNotification();
            if (is_null($recipients)) {
                $recipients = Kwf_Config::getValue('maintenanceJobs.failNotificationRecipient');
            }
            if ($recipients) {
                $mail = new Kwf_Mail();
                $mail->addTo($recipients);
                $mail->setSubject('['.Kwf_Config::getValue('application.name').'] maintenance-job '.$runRow->job.' '.$runRow->status);
                $mail->setBodyText(Kwf_Registry::get('db')->query("SELECT log FROM {$runsModel->getTableName()} WHERE id=?", array($runRow->id))->fetchColumn());
                $mail->send();
            }
        }

    }
}
