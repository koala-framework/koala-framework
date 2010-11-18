<?php
abstract class Vps_Util_PubSubHubbub_AbstractTest extends PHPUnit_Framework_TestCase
{
    protected $_hubApp;
    protected $_hubUrl;
    protected $_testId;
    protected $_testFeedUrl;
    private $_storePath;
    private $_pipes;

    public function setUp()
    {
        $debugOutput = false;
        //echo "setUp\n";

        $this->_storePath = tempnam('/tmp', 'pshb');
        unlink($this->_storePath);
        mkdir($this->_storePath);
        //installation see here: http://code.google.com/p/pubsubhubbub/wiki/DeveloperGettingStartedGuide
        //echo "startHub\n";
        $this->_startHub($debugOutput);

        //echo "test hub\n";
        try {
            $c = file_get_contents($this->_hubUrl);
            $this->assertContains('Welcome to the demo PubSubHubbub reference Hub server', $c);
            //echo "OK\n";
        } catch (Exception $e) {
            $this->fail("Failed starting hub, output: ".stream_get_contents($this->_pipes[1]));
        }

        //echo "write test feed\n";
        $this->_testId = rand(0, 1000000);
        touch('/tmp/lastCallback'.$this->_testId);
        file_put_contents('/tmp/feedRequested'.$this->_testId, 0);

        $domain = Vps_Registry::get('config')->server->domain;
        $urlPrefix = 'http://'.$domain.'/vps/test';
        $this->_testFeedUrl = $urlPrefix.'/vps_util_pub-sub-hubbub_test-feed?id='.$this->_testId;

        $this->_writeTestFeedContent(1);

    }

    private function _startHub($debugOutput)
    {
        $descriptorspec = array();
        if ($debugOutput) {
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

        $address = trim(`hostname`);
        if ($address == 'vivid-test-server') {
            $d = "/var/www/library/pshb/";
        } else {
            $d = "/www/public/library/pshb/";
        }
        $port = Vps_Util_Tcp::getFreePort(8000, $address);
        $cmd = "python2.5 {$d}google_appengine/dev_appserver.py {$d}pubsubhubbub/hub/ ".
               "--port=$port --address=$address --clear_datastore ".
               "--datastore_path=$this->_storePath/dev_appserver.datastore ".
               "--history_path=$this->_storePath/dev_appserver.datastore.history".
               " 2>&1";
        if ($debugOutput) echo "$cmd\n";
        $this->_hubApp = proc_open($cmd, $descriptorspec, $this->_pipes);
        $this->assertTrue(is_resource($this->_hubApp));
        $this->_hubUrl = "http://$address:$port";
        sleep(10);
        $status = proc_get_status($this->_hubApp);
        if (!$status['running']) {
            throw new Vps_Exception('can\'t start pshb server');
        }
    }

    public function tearDown()
    {
        //echo "\n".date('H:i:s')."tearDown\n";

        $start = time();
        $status = proc_get_status($this->_hubApp);
        //echo "\nsending SIGINT to hub\n";
        posix_kill($status['pid'], SIGINT);
        posix_kill($status['pid']+1, SIGINT); //+1 weil sh prozess python startet
        do {
            //echo "waiting while running\n";
            if (time() - $start > 30) {
                //echo "\nsending SIGKILL to hub\n";
                proc_terminate($this->_hubApp, SIGKILL);
                break;
            }
            $status = proc_get_status($this->_hubApp);
            if ($status['running']) sleep(1);
        } while ($status['running']);
        sleep(5);

        //echo "removing datastore\n";
        system("rm -r $this->_storePath");

        //echo "removing feeds\n";
        unlink('/tmp/feed'.$this->_testId);
        unlink('/tmp/lastCallback'.$this->_testId);
        unlink('/tmp/feedRequested'.$this->_testId);
    }

    protected function assertFeedRequested($num)
    {
        $this->assertEquals($num, file_get_contents('/tmp/feedRequested'.$this->_testId));
    }

    protected function _writeTestFeedContent($entries, $advertiseHub = true)
    {
        file_put_contents('/tmp/feed'.$this->_testId, $this->_getTestFeedContent($entries, $advertiseHub));
    }

    protected function _publishTestFeedUpdate()
    {
        $p = new Vps_Util_PubSubHubbub_Publisher($this->_hubUrl.'/');
        $p->publishUpdate($this->_testFeedUrl);
    }


    protected function _getTestFeedContent($entries, $advertiseHub = true)
    {
$ret = '<feed xmlns="http://www.w3.org/2005/Atom">
    <title>niko\'s stream</title>
';
if ($advertiseHub) {
    $ret .= '    <link rel="hub" href="'.htmlspecialchars($this->_hubUrl).'/" />
';
}
$ret .= '    <id>'.htmlspecialchars($this->_testFeedUrl).'</id>
    <author><name>niko</name></author>
';
for ($i=$entries;$i>0;$i--) {
$ret .= '    <entry>
        <title>blub'.$i.'</title>
        <id>'.htmlspecialchars($this->_testFeedUrl).'/'.$i.'</id>
        <link href="'.htmlspecialchars($this->_testFeedUrl).'/'.$i.'" />
        <summary>bleb'.$i.'</summary>
        <updated>2009-10-0'.($i).'T10:23:24+01:00</updated>
    </entry>';
}
$ret .= '
</feed>';
return $ret;
    }

    protected function _runHubTasks($queue)
    {
        $tasks = file_get_contents($this->_hubUrl.'/_ah/admin/tasks?queue='.$queue);
        if (preg_match_all('#<form id="runform\\..*?" action="(.*?)" .*?</form>#s', $tasks, $tasks)) {
            foreach ($tasks[0] as $taskKey => $t) {
                $client = new Zend_Http_Client($this->_hubUrl.$tasks[1][$taskKey]);
                $client->setMethod(Zend_Http_Client::POST);
                $headers = array();
                $taskName = false;
                preg_match_all('#<input type="hidden"\s*name="(.*?)"\s*value="(.*?)"\s*/?>#', $t, $m);
                foreach (array_keys($m[0]) as $i) {
                    $name = $m[1][$i];
                    $value = $m[2][$i];
                    if (substr($name, 0, 7)=='header:') {
                        $name = substr($name, 7);
                        $client->setHeaders($name, $value);
                        if ($name == 'X-AppEngine-TaskName') $taskName = $value;
                    } else if ($name == 'payload') {
                        $client->setRawData($value);
                    }
                }
                $response = $client->request();
                if ($response->getStatus() != 200) {
                    throw new Vps_Exception("$mode failed, response status '{$response->getStatus()}' '{$response->getBody()}'");
                }

                $client = new Zend_Http_Client($this->_hubUrl.'/_ah/admin/tasks');
                $client->setMethod(Zend_Http_Client::POST);
                $client->setParameterPost(array(
                    'queue' => $queue,
                    'task' => $taskName,
                    'action:deletetask' => true
                ));
                $response = $client->request();
                if ($response->getStatus() != 200) {
                    throw new Vps_Exception("$mode failed, response status '{$response->getStatus()}' '{$response->getBody()}'");
                }
            }
        }
    }
}
