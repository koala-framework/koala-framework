<?php
class Vps_Util_Gearman_AdminClient
{
    private $_connection;
    public function getInstance($server = 'localhost')
    {
        static $i;
        if (!isset($i)) {
            $i = new self();
            $c = Vps_Registry::get('config')->server->gearman;
            $server = $c->jobServers->$server;
            $i->_connection = fsockopen($server->host, $server->port, $errno, $errstr, 30);
            if (!$i->_connection) {
                throw new Vps_Exception("Can't connect: $errstr ($errno)");
            }
        }
        return $i;
    }

    public function __destruct()
    {
        fclose($this->_connection);
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
