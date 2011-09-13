<?php
class Vps_Util_Gearman_AdminClient
{
    private $_connection;
    public function getInstance($server = 'localhost')
    {
        static $i;
        if (!isset($i)) {
            self::checkConnection($server);
            $i = new self();
            $c = Vps_Registry::get('config')->server->gearman;
            $server = $c->jobServers->$server;
            if ($server->tunnelUser) {
                $i->_connection = fsockopen('localhost', 4730, $errno, $errstr, 30);
            } else {
                $i->_connection = fsockopen($server->host, $server->port, $errno, $errstr, 30);
            }
            if (!$i->_connection) {
                throw new Vps_Exception("Can't connect: $errstr ($errno)");
            }
        }
        return $i;
    }

    public static function checkConnection($server = 'localhost')
    {
        if (is_string($server)) {
            $server = Vps_Registry::get('config')->server->gearman->jobServers->$server;
        }
        if ($server->tunnelUser) {
            $fp = @fsockopen('localhost', 4730, $errno, $errstr, 5);
            if (!$fp) {
                system("ssh $server->tunnelUser@$server->host -L $server->port:localhost:4730 sleep 60 >application/log/gearman-tunnel.log 2>&1 &");
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
        $prefix = Vps_Registry::get('config')->server->gearman->functionPrefix.'_';
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
