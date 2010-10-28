<?php
class Vps_Model_Mongo_RunTempMongoController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $mongoDir = "/home/niko/mongodb-linux-i686-1.6.1"; //TODO, obviously
        $debugOutput = true;

        $port = $this->_getParam('port');

        if (!file_exists($mongoDir.'/bin/mongod')) {
            throw new PHPUnit_Framework_SkippedTestError('mongo daemon not found');
        }
        $dir = tempnam('/tmp', 'mongodata');
        unlink($dir);
        mkdir($dir);

        $cmd = "$mongoDir/bin/mongod --port=$port --dbpath=$dir";
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
        system("rm -r ".escapeshellarg($dir));

        exit;
    }
}