<?php
class Vps_Model_Mongo_RunTempMongoController extends Vps_Controller_Action
{
    private $_dir;
    private $_proc;

    public function sig_handler($signo)
    {
        if ($signo == SIGTERM) {
            //wenn wir gekillt werden mongo auch mitkillen
            echo "Caught SIGTERM...\n";
            $this->_proc->terminate();
            $this->_proc->close(false);
            system("rm -r ".escapeshellarg($this->_dir));
            exit;
        }
    }

    public function indexAction()
    {
        declare(ticks = 1);
        pcntl_signal(SIGTERM, array($this, "sig_handler"));

        $mongoDir = "/usr";

        $debugOutput = true;

        $port = $this->_getParam('port');

        if (!file_exists($mongoDir.'/bin/mongod')) {
            throw new PHPUnit_Framework_SkippedTestError('mongo daemon not found');
        }
        $this->_dir = tempnam('/tmp', 'mongodata');
        unlink($this->_dir);
        mkdir($this->_dir);

        $cmd = "$mongoDir/bin/mongod --port=$port --dbpath=$this->_dir";
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
        sleep(60*15);

        $this->_proc->terminate();
        $this->_proc->close(false);
        system("rm -r ".escapeshellarg($this->_dir));

        exit;
    }
}