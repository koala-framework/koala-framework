<?php
class Kwf_Util_Gearman_AdminClient
{
    private $_functionPrefix;
    private $_connection;
    public function getInstance($serverKey = 'localhost', $group = null)
    {
        static $i = array();
        $key = $serverKey.'-'.$group;
        if (!isset($i[$key])) {

            $i[$key] = new self();

            $c = Kwf_Util_Gearman_Servers::getServers($group);
            $i[$key]->_functionPrefix = $c['functionPrefix'];
            $server = $c['jobServers'][$serverKey];
            self::checkConnection($server);
            if (isset($server['tunnelUser']) && $server['tunnelUser']) {
                $i[$key]->_connection = fsockopen('localhost', 4730, $errno, $errstr, 30);
            } else {
                $i[$key]->_connection = fsockopen($server['host'], $server['port'], $errno, $errstr, 30);
            }
            if (!$i[$key]->_connection) {
                throw new Kwf_Exception("Can't connect: $errstr ($errno)");
            }
        }
        return $i[$key];
    }

    public function getInstances($group = null)
    {
        $ret[] = array();
        $servers = Kwf_Util_Gearman_Servers::getServers($group);
        foreach(array_keys($servers['jobServers']) as $key) {
            $ret[$key] = self::getInstance($key, $group);
        }
        return $ret;
    }

    public static function checkConnection($server)
    {
        if (isset($server['tunnelUser']) && $server['tunnelUser']) {
            $fp = @fsockopen('localhost', $server['tunnelPort'], $errno, $errstr, 5);
            if (!$fp) {
                system("ssh $server[tunnelUser]@$server[host] -L $server[tunnelPort]:localhost:$server[port] sleep 60 >log/gearman-tunnel.log 2>&1 &");
                sleep(2);
            } else {
                fclose($fp);
            }
        }
    }

    public function __destruct()
    {
        if ($this->_connection) fclose($this->_connection);
    }

    public function getStatus()
    {
        $out = "status\n";
        fwrite($this->_connection, $out);
        $in = '';
        while (!feof($this->_connection)) {
            $in .= fgets($this->_connection, 1024);
            if (substr($in, -3)=="\n.\n") break;
        }
        $in = substr($in, 0, -3);
        $prefix = $this->_functionPrefix.'_';
        $ret = array();
        foreach (explode("\n", $in) as $line) {
            $line = trim($line);
            $line = explode("\t", $line);
            if (substr($line[0], 0, strlen($prefix))==$prefix) {
                $ret[substr($line[0], strlen($prefix))] = array(
                    'total' => $line[1],
                    'running' => $line[2],
                    'availableWorkers' => $line[3]
                );
            }
        }
        return $ret;
    }

    public function getWorkers()
    {
        $out = "workers\n";
        fwrite($this->_connection, $out);
        $in = '';
        while (!feof($this->_connection)) {
            $in .= fgets($this->_connection, 1024);
            if (substr($in, -3)=="\n.\n") break;
        }
        $in = substr($in, 0, -3);
        $prefix = $this->_functionPrefix.'_';
        $ret = array();
        foreach (explode("\n", $in) as $line) {
            $line = trim($line);
            $line = explode("\t", $line);
            foreach ($line as $i) {
                if (preg_match('#(.*) (.*) (.*) : ?(.*)#', $i, $m)) {
                    $ret[] = array(
                        'fd' => $m[1],
                        'ipAddress' => $m[2],
                        'clientId' => $m[3],
                        'function' => $m[4]
                    );
                } else {
                    throw new Kwf_Exception("Can't match line");
                }
            }
        }
        return $ret;
    }

    public function setMaxQueue($functionName, $queueSize)
    {
        $prefix = $this->_functionPrefix.'_';
        $out = "maxqueue $prefix$functionName";
        if (!is_null($queueSize)) $out .= "$queueSize";
        $out .= "\n";
        fwrite($this->_connection, $out);
        $in = fgets($this->_connection, 1024);
        if (trim($in) != "OK") {
            throw new Kwf_Exception("maxqueue command failed: $in");
        }
    }
}
