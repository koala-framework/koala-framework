<?php
class Vps_Model_Mongo_TestModel extends Vps_Model_Mongo
{
    protected $_collection = 'foo';
    static private $_proc;

    public function __construct()
    {
        $debugOutput = false;

        $mongoDir = "/usr";
        if (!file_exists($mongoDir.'/bin/mongod')) {
            throw new PHPUnit_Framework_SkippedTestError('mongo daemon not found');
        }
        static $m;
        if (!isset($m)) {
            $port = Vps_Util_Tcp::getFreePort(rand(27020, 30000));
            $cmd = "vps test forward --controller=vps_model_mongo_run-temp-mongo --port=$port";
            if ($debugOutput) {
                echo $cmd."\n";
                $descriptorspec = array(
                    1 => STDOUT,
                    2 => STDOUT
                );
            } else {
                $cmd .= " 2>&1 >/dev/null";
                $descriptorspec = array(
                    1 => array('pipe', 'w'),
                    2 => STDOUT //should be empty
                );
            }
            self::$_proc = new Vps_Util_Proc($cmd, $descriptorspec);
            sleep(3);
            $m = new Mongo("mongodb://localhost:$port");

            register_shutdown_function(array('Vps_Model_Mongo_TestModel', 'shutDown'));
        }
        $db = $m->selectDB("modeltest");
        $db->selectCollection($this->_collection)->drop();
        $config = array(
            'db' => $db
        );
        parent::__construct($config);
    }

    public function cleanUp()
    {
    }

    public static function shutDown()
    {
        self::$_proc->terminate();
        self::$_proc->close(false);
    }
}
