<?php
class Vps_Controller_Action_Cli_Web_ProcessControlController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "start background processes used by this web";
    }

    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'debug',
                'value'=> true,
                'valueOptional' => true,
            )
        );
    }

    private $_commands;
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_commands = Vps_Registry::get('config')->processControl->toArray();
        foreach ($this->_commands as &$c) {
            if (!isset($c['count'])) $c['count'] = 1;
        }
    }

    public function indexAction()
    {
        $this->_start();
        exit;
    }

    public function stopAction()
    {
        $this->_stop();
        exit;
    }

    public function restartAction()
    {
        $this->_stop();
        $this->_start();
        exit;
    }

    public function statusAction()
    {
        $processes = Vps_Util_Process::getRunningWebProcesses();
        foreach ($this->_commands as $requiredCmd) {
            $found = false;
            foreach ($processes as $p) {
                if ($p['cmd'] == $requiredCmd['cmd']) {
                    $found = true;
                    echo "[$p[pid]] $p[prettyTime] $p[cmd]\n";
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
            file_put_contents("application/log/$requiredCmd[cmd].log", '');
            file_put_contents("application/log/$requiredCmd[cmd].err", '');
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
                    'file' => "application/log/$requiredCmd[cmd].log",
                    'initialRead' => 80
                );
            }
            $files[] = array(
                'prefix' => "[E/$requiredCmd] ",
                'file' => "application/log/$requiredCmd.err",
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
        $processes = Vps_Util_Process::getRunningWebProcesses();
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
                $cmd = "php bootstrap.php $requiredCmd[cmd] ";
                if ($this->_getParam('debug')) $cmd .= "--debug ";
                $cmd .= " 2>>application/log/$requiredCmd[cmd].err";
                $cmd .= " 1>>application/log/$requiredCmd[cmd].log";
                $cmd .= " &";
                //if (!$this->_getParam('silent')) echo $cmd."\n";
                passthru($cmd);

                $runningCount++;
            }
        }
    }

    public function _stop()
    {
        $killed = array();
        $processes = Vps_Util_Process::getRunningWebProcesses();
        foreach ($this->_commands as $requiredCmd) {
            foreach ($processes as $p) {
                if ($p['cmd'] == $requiredCmd['cmd']) {
                    if (isset($requiredCmd['shutdownFunction'])) {
                        if (!$this->_getParam('silent')) echo "calling $requiredCmd[shutdownFunction]\n";
                        $fn = explode('::', $requiredCmd['shutdownFunction']);
                        call_user_func($fn);
                    } else {
                        if (!$this->_getParam('silent')) echo "kill $p[pid] $p[cmd] $p[args]\n";
                        posix_kill($p['pid'], SIGTERM);
                        $killed[] = $p['pid'];
                        foreach ($p['childPIds'] as $pid) {
                            posix_kill($pid, SIGTERM);
                            $killed[] = $pid;
                        }
                    }
                }
            }
        }
        $start = time();
        while(true) {
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
