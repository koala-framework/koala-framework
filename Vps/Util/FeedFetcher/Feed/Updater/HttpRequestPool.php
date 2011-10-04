<?php
class Vps_Util_FeedFetcher_Feed_Updater_HttpRequestPool extends HttpRequestPool
{
    private $_debug = false;
    private $_updateServer;

    public function __construct($updateServer)
    {
        $this->_updateServer = $updateServer;
    }

    public function setDebug($debug)
    {
        $this->_debug = $debug;
    }


    public function start($timeLimit = 0)
    {
        $start = microtime(true);

        $updatedFeeds = 0;

        $requestor = Vps_Http_Pecl_Requestor::getInstance();
        $rows = Vps_Model_Abstract::getInstance('feeds')->getUpdateRows(5, $this->_debug);
        foreach ($rows as $f) {
            $updatedFeeds++;
            if ($this->_debug) echo (microtime(true)-$start)." fetching $f->url\n";
            $f->last_update = date('Y-m-d H:i:s');
            $f->save();
            $this->attach(new Vps_Util_FeedFetcher_Feed_Updater_HttpRequest($f));
        }

        while (true) {
            //if ($this->_debug) echo (microtime(true)-$start)."\n"; flush();
            try {
                if (!$this->socketPerform()) {
                    break;
                }
            } catch (Exception $e) {
                $e = new Vps_Util_FeedFetcher_Exception_FeedUpdateOther($e);
                $e->log();
            }
            if (!$this->socketSelect()) {
                $e = new Vps_Util_FeedFetcher_Exception_FeedUpdateOther($e);
                $e->log();
            }
            $updatedFeeds += $this->_processFinishedRequests($start, $timeLimit);

            if (false && $this->_debug && Vps_Util_FeedFetcher_Exception_FeedUpdateOther::$logged) {
                echo "EXCEPTIONS:\n";
                foreach (Vps_Util_FeedFetcher_Exception_FeedUpdateOther::$logged as $e) {
                    echo $e->getException();
                    echo "----------------\n";
                }
                Vps_Util_FeedFetcher_Exception_FeedUpdateOther::$logged = array();
            }
        }
        $updatedFeeds += $this->_processFinishedRequests($start, $timeLimit);

        return $updatedFeeds;
    }

    private function _processFinishedRequests($start, $timeLimit)
    {
        $updatedFeeds = 0;
        foreach ($this->getFinishedRequests() as $request) {
            $this->detach($request);
            $f = $request->getFeed();
            try {
                $response = $request->getResponseMessage();
            } catch (Exception $e) {
                $response = null;
            }
            $response = new Vps_Http_Pecl_Requestor_Response($response);
            $error = false;
            Vps_Util_FeedFetcher_Feed::handleResponse($f, $this->_updateServer, $request->getStart(), $response, $error);
            if ($this->_debug) {
                $duration = microtime(true)-$request->getStart();
                echo "\n".(microtime(true)-$start) ." [";
                echo $error ? 'ERROR' : 'OK';
                echo "] feched in $duration $f->url\n";
            }

            if (!$timeLimit || microtime(true)-$start < $timeLimit) {
                $rows = Vps_Model_Abstract::getInstance('feeds')->getUpdateRows(1, $this->_debug);
                if ($rows) {
                    $f = $rows[0];
                    $updatedFeeds++;
                    if ($this->_debug) echo (microtime(true)-$start)." fetching $f->url\n";
                    $f->last_update = date('Y-m-d H:i:s');
                    $f->save();
                    $this->attach(new Vps_Util_FeedFetcher_Feed_Updater_HttpRequest($f));
                }
            }
        }
        return $updatedFeeds;
    }
}
