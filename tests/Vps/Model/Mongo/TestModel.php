<?php
class Vps_Model_Mongo_TestModel extends Vps_Model_Mongo
{
    private $_dir;
    private $_proc;
    private $_port;

    public function __construct()
    {
        $mongoDir = "/home/niko/mongodb-linux-i686-1.6.1"; //TODO, obviously
        $timeLimit = 20;
        $debugOutput = false;

        if (!file_exists($mongoDir.'/bin/mongod')) {
            throw new PHPUnit_Framework_SkippedTestError('mongo daemon not found');
        }
        $this->_dir = tempnam('/tmp', 'mongodata');
        unlink($this->_dir);
        mkdir($this->_dir);

        $this->_port = Vps_Util_Tcp::getFreePort(27020);
        $cmd = "timeout -15 $timeLimit $mongoDir/bin/mongod --port=$this->_port --dbpath=$this->_dir";
        $descriptorspec = array();
        if ($debugOutput) {
            echo $cmd."\n";
            $descriptorspec = array(
                1 => STDOUT,
                2 => STDOUT
            );
        } else {
            $descriptorspec = array(
                1 => array('pipe', 'w'),
                2 => STDOUT //should be empty
            );
        }
        $this->_proc = new Vps_Util_Proc($cmd, $descriptorspec);
        sleep(1);

        $m = new Mongo("mongodb://localhost:$this->_port");
        $db = $m->selectDB("modeltest");

        $config = array(
            'db' => $db
        );
        parent::__construct($config);
    }

    public function cleanUp()
    {
        $this->_proc->terminate();
        $this->_proc->close(false);
        system("rm -r ".escapeshellarg($this->_dir));
    }
}
