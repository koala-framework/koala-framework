<?php
class Kwf_Controller_Action_Cli_Web_MaintenanceJobsController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "execute mainteanance commands, should be run by process-control";
    }

    public function runAction()
    {
        $debug = $this->_getParam('debug');

        if (file_exists('temp/shutdown-maintenance')) {
            unlink('temp/shutdown-maintenance');
        }

        $lastDailyRun = null;
        if (file_exists('temp/maintenance-daily-run')) {
            $lastDailyRun = file_get_contents('temp/maintenance-daily-run');
            if ($debug) echo "last daily run: ".date('Y-m-d H:i:s', $lastDailyRun)."\n";
        }

        $lastHourlyRun = null;
        if (file_exists('temp/maintenance-hourly-run')) {
            $lastHourlyRun = file_get_contents('temp/maintenance-hourly-run');
            if ($debug) echo "last hourly run: ".date('Y-m-d H:i:s', $lastHourlyRun)."\n";
        }

        $dailyMaintenanceWindowStart = "01:00"; //don't set before 00:00
        $dailyMaintenanceWindowEnd = "05:00";

        $nextDailyRun = null;
        $lastMinutelyRun = null;
        while (true) {
            if (!$nextDailyRun) {
                if ($lastDailyRun && $lastDailyRun > strtotime($dailyMaintenanceWindowStart)) { //today already run
                    //maintenance window of tomorrow
                    $nextDailyRun = rand(strtotime("tomorrow $dailyMaintenanceWindowStart"), strtotime("tomorrow $dailyMaintenanceWindowEnd"));
                } else { //not yet run or today not yet run
                    if (time() < strtotime($dailyMaintenanceWindowEnd)) { //window not yet over for today
                        //maintenance window of today
                        $nextDailyRun = rand(max(time(), strtotime($dailyMaintenanceWindowStart)), strtotime($dailyMaintenanceWindowEnd));
                    } else {
                        //maintenance window of tomorrow
                        $nextDailyRun = rand(strtotime("tomorrow $dailyMaintenanceWindowStart"), strtotime("tomorrow $dailyMaintenanceWindowEnd"));
                    }
                }
                if ($debug) echo "Next daily run: ".date('Y-m-d H:i:s', $nextDailyRun)."\n";
            }

            Kwf_Util_Maintenance_Dispatcher::executeJobs(Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_SECONDS, $debug);
            if (!$lastMinutelyRun || time()-$lastMinutelyRun > 60) {
                if (!$lastMinutelyRun) {
                    sleep(rand(0, 45)); // Prevents running jobs of multiple webs in the same second
                }
                $lastMinutelyRun = time();
                Kwf_Util_Maintenance_Dispatcher::executeJobs(Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_MINUTELY, $debug);

                //discard connection to database to reconnect on next job run
                //avoids problems with auto closed connections due to inactivity
                if (function_exists('gc_collect_cycles')) {
                    Kwf_Model_Abstract::clearAllRows();
                    Kwf_Model_Abstract::clearInstances();
                    gc_collect_cycles();
                    Kwf_Registry::getInstance()->offsetUnset('db');
                    Kwf_Registry::getInstance()->offsetUnset('dao');
                }
            }
            Kwf_Component_Data_Root::getInstance()->freeMemory();

            if (!$lastHourlyRun || time()-$lastHourlyRun > 3600) {
                if ($debug) echo date('Y-m-d H:i:s')." execute hourly jobs\n";
                $lastHourlyRun = time();
                file_put_contents('temp/maintenance-hourly-run', $lastHourlyRun);
                Kwf_Util_Maintenance_Dispatcher::executeJobs(Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_HOURLY, $debug);
            }

            Kwf_Component_Data_Root::getInstance()->freeMemory();

            if (time() > $nextDailyRun) {
                if ($debug) echo date('Y-m-d H:i:s')." execute daily jobs\n";
                $lastDailyRun = time();
                file_put_contents('temp/maintenance-daily-run', $lastDailyRun);
                $nextDailyRun = null;
                Kwf_Util_Maintenance_Dispatcher::executeJobs(Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_DAILY, $debug);
            }
            sleep(10);
        }
    }

    public function runDailyAction()
    {
        $debug = $this->_getParam('debug');
        Kwf_Util_Maintenance_Dispatcher::executeJobs(Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_DAILY, $debug);
        exit;
    }

    public function showJobsAction()
    {
        echo "List of available jobs:\n";
        foreach (Kwf_Util_Maintenance_Dispatcher::getAllMaintenanceJobs() as $job) {
            echo "  ".get_class($job)."\n";
        }
        exit;
    }

    public function runJobAction()
    {
        $debug = $this->_getParam('debug');
        $jobClassName = $this->_getParam('job');
        if (!$jobClassName) {
            echo "Missing parameter job.\n";
            exit;
        }
        $jobFound = false;
        foreach (Kwf_Util_Maintenance_Dispatcher::getAllMaintenanceJobs() as $job) {
            if (get_class($job) === $jobClassName) {
                $jobFound = true;
                break;
            }
        }
        if (!$jobFound) {
            echo "Job not found. Should be the classname.\n";
            exit;
        }

        $t = microtime(true);
        $job = new $jobClassName();
        $job->execute($debug);
        Kwf_Events_ModelObserver::getInstance()->process();
        $t = microtime(true)-$t;
        if ($debug) echo "executed ".get_class($job)." in ".round($t, 3)."s\n";
        exit;
    }
}
