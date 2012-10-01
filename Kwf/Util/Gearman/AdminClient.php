<?php
class Kwf_Util_Gearman_AdminClient
{
    private $_functionPrefix;
    private $_connection;
    public function getInstance($serverKey = 'localhost', $group = null)
    {
        static $i;
        if (!isset($i)) {

            $i = new self();

            $c = Kwf_Util_Gearman_Servers::getServers($group);
            $i->_functionPrefix = $c['functionPrefix'];
            $server = $c['jobServers'][$serverKey];
            self::checkConnection($server);
            if (isset($server['tunnelUser']) && $server['tunnelUser']) {
                $i->_connection = fsockopen('localhost', 4730, $errno, $errstr, 30);
            } else {
                $i->_connection = fsockopen($server['host'], $server['port'], $errno, $errstr, 30);
            }
            if (!$i->_connection) {
                throw new Kwf_Exception("Can't connect: $errstr ($errno)");
            }
        }
        return $i;
    }

    public function getInstances($group = null)
    {
        $ret[] = array();
        $servers = Kwf_Util_Gearman_Servers::getServers($group);
        foreach(array_keys($servers['jobServers']) as $key) {
            $ret[$key] = self::getInstance($key);
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
}
