<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Kwf_Controller_Action_Cli_Web_MaintenanceJobsController extends Kwf_Controller_Action_Cli_Abstract
{
    const TIME_TO_KEEP_PREVIOUS_RUNS_IN_DAYS = 7;

    public static function getHelp()
    {
        return "execute mainteanance commands, should be run by process-control";
    }

    public function runAction()
    {
        $debug = $this->_getParam('debug');
        $output = $this->_getParam('output');

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

        $lastCustomRun = array();
        if (file_exists('temp/maintenance-custom-run')) {
            $lastCustomRun = json_decode(file_get_contents('temp/maintenance-custom-run'), true);
        }

        $dailyMaintenanceWindowStart = "01:00"; //don't set before 00:00
        $dailyMaintenanceWindowEnd = "05:00";

        $nextDailyRun = null;
        $nextCustomRun = array();
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

            Kwf_Util_Maintenance_Dispatcher::executeJobs(Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_SECONDS, $debug, $output);
            if (!$lastMinutelyRun || time()-$lastMinutelyRun > 60) {
                $lastMinutelyRun = time();
                Kwf_Util_Maintenance_Dispatcher::executeJobs(Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_MINUTELY, $debug, $output);

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
                Kwf_Util_Maintenance_Dispatcher::executeJobs(Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_HOURLY, $debug, $output);

                //set to vanished if last_process_seen has not been updated for 60 seconds
                $s = new Kwf_Model_Select();
                $s->whereEquals('status', 'running');
                $s->where(new Kwf_Model_Select_Expr_Lower('last_process_seen', new Kwf_DateTime(time()-60)));
                foreach (Kwf_Model_Abstract::getInstance('Kwf_Util_Maintenance_JobRunsModel')->getRows($s) as $row) {
                    $row->status = 'vanished';
                    $row->save();
                }

                //delete runs older than a week
                $s = new Kwf_Model_Select();
                $s->where(new Kwf_Model_Select_Expr_Lower('start', new Kwf_DateTime(time() - self::getTimeToKeepPreviousRunsInSeconds())));
                Kwf_Model_Abstract::getInstance('Kwf_Util_Maintenance_JobRunsModel')->deleteRows($s);
            }

            Kwf_Component_Data_Root::getInstance()->freeMemory();

            if (time() > $nextDailyRun) {
                if ($debug) echo date('Y-m-d H:i:s')." execute daily jobs\n";
                $lastDailyRun = time();
                file_put_contents('temp/maintenance-daily-run', $lastDailyRun);
                $nextDailyRun = null;
                Kwf_Util_Maintenance_Dispatcher::executeJobs(Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_DAILY, $debug, $output);
            }

            Kwf_Component_Data_Root::getInstance()->freeMemory();

            foreach (Kwf_Util_Maintenance_Dispatcher::getAllMaintenanceJobIdentifiers() as $jobIdentifier) {
                $job = Kwf_Util_Maintenance_Job_AbstractBase::getInstance($jobIdentifier);

                if ($job->getFrequency() == Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_CUSTOM) {
                    $jobClass = get_class($job);

                    if (!array_key_exists($jobClass, $lastCustomRun)) $lastCustomRun[$jobClass] = null;
                    if (!array_key_exists($jobClass, $nextCustomRun)) $nextCustomRun[$jobClass] = $job->getNextRuntime($lastCustomRun[$jobClass]);

                    if (time() <= $nextCustomRun[$jobClass]) continue;

                    $lastCustomRun[$jobClass] = time();
                    file_put_contents('temp/maintenance-custom-run', json_encode($lastCustomRun));
                    $nextCustomRun[$jobClass] = $job->getNextRuntime($lastCustomRun[$jobClass]);

                    if (!$job->hasWorkload()) continue;
                    if ($debug) echo "execute custom job {$jobClass}\n";
                    Kwf_Util_Maintenance_Dispatcher::executeJob($jobIdentifier, $debug, $output);
                }
            }

            sleep(10);
        }
    }

    public function runDailyAction()
    {
        $debug = $this->_getParam('debug');
        Kwf_Util_Maintenance_Dispatcher::executeJobs(Kwf_Util_Maintenance_Job_Abstract::FREQUENCY_DAILY, $debug, true);
        exit;
    }

    public function showJobsAction()
    {
        echo "List of available jobs:\n";
        foreach (Kwf_Util_Maintenance_Dispatcher::getAllMaintenanceJobIdentifiers() as $jobIdentifier) {
            echo "  {$jobIdentifier}\n";
        }
        exit;
    }

    public function runJobAction()
    {
        $debug = $this->_getParam('debug');
        $jobIdentifier = $this->_getParam('job');
        if (!$jobIdentifier) {
            echo "Missing parameter job.\n";
            exit;
        }
        $jobFound = false;
        foreach (Kwf_Util_Maintenance_Dispatcher::getAllMaintenanceJobIdentifiers() as $identifier) {
            if ($identifier === $jobIdentifier) {
                $jobFound = true;
                break;
            }
        }
        if (!$jobFound) {
            echo "Job not found. Should be the classname.\n";
            exit;
        }

        Kwf_Util_Maintenance_Dispatcher::executeJob($jobIdentifier, $debug, true);
        Kwf_Events_ModelObserver::getInstance()->process();
        exit;
    }

    public function internalRunJobAction()
    {
        $debug = $this->_getParam('debug');
        $runId = $this->_getParam('runId');
        $runRow = Kwf_Model_Abstract::getInstance('Kwf_Util_Maintenance_JobRunsModel')->getRow($runId);
        $jobIdentifier = $runRow->job;
        $job = Kwf_Util_Maintenance_Job_AbstractBase::getInstance($jobIdentifier);
        $job->setDebug($debug);
        $job->setJobRun($runRow);

        $progressSteps = $job->getProgressSteps();
        $progressBar = null;
        if ($progressSteps) {
            $adapter = new Kwf_Util_Maintenance_ProgressBarAdapter($runRow);
            if ($debug) {
                $adapter = new Kwf_Util_ProgressBar_Adapter_Composite(array(
                    $adapter,
                    new Zend_ProgressBar_Adapter_Console()
                ));
            }
            $progressBar = new Zend_ProgressBar($adapter, 0, $progressSteps);
            $runRow->progress = 0;
            $runRow->save();
            $job->setProgressBar($progressBar);
        }
        if ($job instanceof KwfBundle\MaintenanceJobs\AbstractJob) {
            $logger = new Logger('name');
            $level = Logger::INFO;
            if ($debug) {
                $level = Logger::DEBUG;
            }
            $handler = new StreamHandler('php://stdout', $level);
            $handler->setFormatter(new LineFormatter("%message% %context% %extra%\n", null, true, true));
            $logger->pushHandler($handler);
            $job->execute($logger);
        } else {
            $job->execute($debug);
        }

        Kwf_Events_ModelObserver::getInstance()->process();
        exit;
    }

    public static function getTimeToKeepPreviousRunsInSeconds()
    {
        $daysFromSettings = Kwf_Config::getValue('maintenanceJobs.timeToKeepPreviousRunsInDays');
        $resolvedDays = (intval($daysFromSettings) >= 1) ? intval($daysFromSettings) : self::TIME_TO_KEEP_PREVIOUS_RUNS_IN_DAYS; // force minimum to be one day
        return $resolvedDays * 24 * 60 * 60;
    }
}
