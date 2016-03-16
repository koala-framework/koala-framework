<?php
class Kwf_Controller_Action_Cli_Web_ProcessControlController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "start background processes used by this web";
    }

    public static function getHelpOptions()
    {
        return array();
    }

    private $_commands;
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_commands = array();
        if (Kwf_Registry::get('config')->processControl) {
            $this->_commands = Kwf_Registry::get('config')->processControl->toArray();
        }
        foreach ($this->_commands as $k=>$c) {
            if (!$c) {
                unset($this->_commands[$k]);
                continue;
            }
            if (!isset($c['count'])) $this->_commands[$k]['count'] = 1;
        }
    }

    public function indexAction()
    {
        if (!ini_get('short_open_tag')) {
            throw new Kwf_Exception_Client("You must set short_open_tag to on in php.ini");
        }

        $this->_start();

        if (file_exists('log/process-control-log-status')) {
            $logFileSendPos = unserialize(file_get_contents('log/process-control-log-status'));
        } else {
            $logFileSendPos = array();
        }

        $logFiles = array();
        foreach ($this->_commands as $requiredCmd) {
            $logFiles[] = "log/$requiredCmd[cmd].log";
            $logFiles[] = "log/$requiredCmd[cmd].err";
        }
        $msg = '';
        foreach ($logFiles as $logFile) {
            if (!file_exists($logFile)) continue;
            $fileSize = filesize($logFile);
            if (!isset($logFileSendPos[$logFile]) || $fileSize != $logFileSendPos[$logFile]) {
                if ($this->_getParam('debug')) echo "$logFile: ".$fileSize." bytes\n";;
                $prevFileSize = isset($logFileSendPos[$logFile]) ? isset($logFileSendPos[$logFile]) : 0;
                if ($fileSize < $prevFileSize) $prevFileSize = 0; //log file was truncated or deleted
                $fp = fopen($logFile, 'r');
                $msg .= date('Y-m-d H:i:s')." $logFile:\n";
                $msg .= stream_get_contents($fp, -1, $prevFileSize);
                fclose($fp);
                $logFileSendPos[$logFile] = $fileSize;
            }
        }
        if ($msg) {
            echo $msg;
        }
        file_put_contents('log/process-control-log-status', serialize($logFileSendPos));

        exit;
    }

    public function stopAction()
    {
        $onlyCommandKey = $this->_getParam('command');
        $this->_stop($onlyCommandKey);
        exit;
    }

    public function restartAction()
    {
        $onlyCommandKey = $this->_getParam('command');
        $this->_stop($onlyCommandKey);
        $this->_start();
        exit;
    }

    public function statusAction()
    {
        $allProcesses = Kwf_Util_Process::getRunningProcesses();
        $webProcesses = Kwf_Util_Process::getRunningWebProcesses();
        foreach ($this->_commands as $requiredCmd) {
            $found = false;
            foreach ($webProcesses as $p) {
                if ($p['cmd'] == $requiredCmd['cmd']) {
                    $found = true;
                    echo "[$p[pid]] ".Kwf_View_Helper_FileSize::fileSize($p['memory'])." $p[prettyTime] $p[cmd]\n";
                    foreach ($p['childPIds'] as $pid) {
                        $cp = $allProcesses[$pid];
                        echo "  [$cp[pid]] ".Kwf_View_Helper_FileSize::fileSize($cp['memory'])." $cp[prettyTime] $cp[cmd]\n";
                    }
                }
            }
            if (!$found) echo "NOT RUNNING: $requiredCmd[cmd]\n";
        }
        exit;
    }

    public function errcatAction()
    {
        set_time_limit(0);
        $this->_logcat(false);
    }

    public function logcatAction()
    {
        set_time_limit(0);
        $this->_logcat(true);
    }

    public function logclearAction()
    {
        foreach ($this->_commands as $requiredCmd) {
            file_put_contents("log/$requiredCmd[cmd].log", '');
            file_put_contents("log/$requiredCmd[cmd].err", '');
        }
        exit;
    }

    private function _logcat($includeLogFiles)
    {
        $files = array();
        foreach ($this->_commands as $requiredCmd) {
            if ($includeLogFiles) {
                $files[] = array(
                    'prefix' => "[L/$requiredCmd[cmd]] ",
                    'file' => "log/$requiredCmd[cmd].log",
                    'initialRead' => 80
                );
            }
            $files[] = array(
                'prefix' => "[E/$requiredCmd[cmd]] ",
                'file' => "log/$requiredCmd[cmd].err",
                'initialRead' => 1024
            );
        }
        foreach ($files as &$file) {
            if (!file_exists($file['file'])) touch($file['file']);
            $file['pos'] = filesize($file['file'])-$file['initialRead'];
        }
        while(true) {
            foreach ($files as &$file) {
                if (filesize($file['file']) < $file['pos']) $file['pos'] = 0;
                $fp = fopen($file['file'], 'r');
                fseek($fp, $file['pos']);
                while (!feof($fp)) {
                    $line = fgets($fp);
                    if ($line !== false) {
                        echo $file['prefix'].trim($line)."\n";
                    }
                }
                $file['pos'] = ftell($fp);
                fclose($fp);
            }
            sleep(1);
        }
    }

    private function _start()
    {
        $processes = Kwf_Util_Process::getRunningWebProcesses();
        foreach ($this->_commands as $requiredCmd) {
            $runningCount = 0;
            foreach ($processes as $p) {
                if ($p['cmd'] == $requiredCmd['cmd']) {
                    if ($this->_getParam('debug')) echo "[$p[pid]] $p[prettyTime] $p[cmd]\n";
                    $runningCount++;
                }
            }
            while ($runningCount < $requiredCmd['count']) {

                if (!$this->_getParam('silent')) echo "Process $requiredCmd[cmd] isn't running. Starting...\n";
                $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php $requiredCmd[cmd] ";
                if ($this->_getParam('debug')) $cmd .= "--debug ";
                $cmd .= " 2>>".escapeshellarg("log/$requiredCmd[cmd].err");
                $cmd .= " 1>>".escapeshellarg("log/$requiredCmd[cmd].log");
                $cmd .= " &";
                //if (!$this->_getParam('silent')) echo $cmd."\n";
                passthru($cmd);

                $runningCount++;
            }
        }
    }

    public function _stop($onlyCommandKey = null)
    {
        $killed = array();
        $processes = Kwf_Util_Process::getRunningWebProcesses();
        foreach ($this->_commands as $commandKey=>$requiredCmd) {
            if ($onlyCommandKey) {
                if ($onlyCommandKey != $commandKey) {
                    continue;
                }
            }
            foreach ($processes as $p) {
                if ($p['cmd'] == $requiredCmd['cmd']) {
                    if (isset($requiredCmd['shutdownFunction'])) {
                        if (!$this->_getParam('silent')) echo "calling $requiredCmd[shutdownFunction] to shutdown $p[pid]\n";
                        $fn = explode('::', $requiredCmd['shutdownFunction']);
                        call_user_func($fn);
                        $killed[] = $p['pid'];
                    } else {
                        if (!$this->_getParam('silent')) echo "kill $p[pid] $p[cmd] $p[args]\n";
                        system("kill $p[pid]");
                        $killed[] = $p['pid'];
                        foreach ($p['childPIds'] as $pid) {
                            if (!$this->_getParam('silent')) echo "    kill child process $pid\n";
                            system("kill $pid");
                            $killed[] = $pid;
                        }
                    }
                }
            }
        }
        $start = time();
        while(true) {
            $pids = array();
            exec('ps ax -o pid', $pids);
            foreach ($pids as &$i) $i = (int)$i;
            $allDone = true;
            foreach ($killed as $pid) {
                if (in_array($pid, $pids)) {
                    $allDone = false;
                    break;
                }
            }
            if ($allDone) break;
            if (time()-$start > 10) {
                echo "timeout while waiting for: ";
                foreach ($killed as $pid) {
                    if (in_array($pid, $pids)) {
                        echo $pid." ";
                    }
                }
                echo "\n";
                break;
            }
            usleep(100*1000);
        }
    }
}
