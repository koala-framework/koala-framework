<?php
abstract class Vps_Util_PubSubHubbub_AbstractTest extends PHPUnit_Framework_TestCase
{
    protected $_hubApp;
    protected $_hubUrl;
    protected $_testId;
    protected $_testFeedUrl;

    public function setUp()
    {
        $debugOutput = false;

        $descriptorspec = array();
        if ($debugOutput) {
            $descriptorspec = array(
                1 => STDOUT,
                2 => STDOUT
            );
        } else {
            $descriptorspec = array(
                1 => array('file', '/dev/null', 'w'),
                2 => array('file', '/dev/null', 'w')
            );
        }

        $port = rand(8000, 10000);
        $address = trim(`hostname`);
        if ($address == 'vivid-test-server') {
            $d = "/var/www/library/pshb/";
        } else {
            $d = "/www/public/library/pshb/";
        }

        //installation see here: http://code.google.com/p/pubsubhubbub/wiki/DeveloperGettingStartedGuide
        $cmd = "python2.5 {$d}google_appengine/dev_appserver.py {$d}pubsubhubbub/hub/ ".
               "--port=$port --address=$address --clear_datastore";
        $this->_hubApp = proc_open($cmd, $descriptorspec, $pipes);
        $this->_hubUrl = "http://$address:$port";
        sleep(1);
        $status = proc_get_status($this->_hubApp);
        if (!$status['running']) {
            //try again with differnet port
            $port = rand(8000, 10000);
            $cmd = "python2.5 {$d}google_appengine/dev_appserver.py {$d}pubsubhubbub/hub/ ".
                "--port=$port --address=$address --clear_datastore";
            $this->_hubApp = proc_open($cmd, $descriptorspec, $pipes);
            $this->_hubUrl = "http://$address:$port";
            sleep(1);
            $status = proc_get_status($this->_hubApp);
        }
        $this->assertTrue($status['running']);

        $this->_testId = rand(0, 1000000);
        touch('/tmp/lastCallback'.$this->_testId);
        file_put_contents('/tmp/feedRequested'.$this->_testId, 0);

        $domain = Vps_Registry::get('config')->server->domain;
        $urlPrefix = 'http://'.$domain.'/vps/test';
        $this->_testFeedUrl = $urlPrefix.'/vps_util_pub-sub-hubbub_test-feed?id='.$this->_testId;

        $this->_writeTestFeedContent(1);

    }

    public function tearDown()
    {
        unlink('/tmp/feed'.$this->_testId);
        unlink('/tmp/lastCallback'.$this->_testId);
        unlink('/tmp/feedRequested'.$this->_testId);

        proc_terminate($this->_hubApp, SIGTERM);
        do {
            $status = proc_get_status($this->_hubApp);
            sleep(1);
        } while ($status['running']);
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
