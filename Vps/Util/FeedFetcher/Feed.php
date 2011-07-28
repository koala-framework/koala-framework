<?php
class Vps_Util_FeedFetcher_Feed
{
    const UPDATE_SUCCESS_NO_NEW_ENTRIES = 'noNewEntries';
    const UPDATE_SUCCESS_NEW_ENTRIES = 'newEntries';
    const UPDATE_ERROR = 'error';
    const UPDATE_NOT_MODIFIED = 'notModified';

    private static $_fetchProxyScripts;

    public static function createRequest($feedId, $url = null)
    {
        $feed = Vps_Util_FeedFetcher_Feed_Cache::getInstance()->load(self::getCacheId($feedId));
        if (!$url) {
            if ($feed && isset($feed['url']) ) { //isset url ist f端r legacy caches
                $url = $feed['url'];
            }
        }
        $url = self::getRequestUrl($feedId, $url);
        return new HttpRequest($url, HTTP_METH_GET, self::getRequestOptions($feed));
    }

    /**
     * @param array mit url, kann false sein
     */
    public static function getRequestUrl($feedId, $url = null)
    {
        if (!$url) {
            $row = Vps_Model_Abstract::getInstance('feeds')->getRow($feedId);
            $url = $row->url;
        }
        if (!$url) {
            throw new Vps_Exception("Unknown url");
        }
        $proxies = self::getFetchProxyScripts();
        if ($proxies) {
            $proxyDomains = array(
                '#google\.[a-z]+$#',
                '#twitter\.com$#'
            );
            $useProxy = false;
            $domain = parse_url($url, PHP_URL_HOST);
            foreach ($proxyDomains as $d) {
                if (preg_match($d, $domain)) {
                    $useProxy = true;
                }
            }
            if ($useProxy && rand(0,1)==0) {
                shuffle($proxies);
                $url = $proxies[0]['url'].'?url='.rawurlencode($url).'&hash='.md5($url.'w3rklslfsdlj');
            }
        }
        return $url;
    }

    public static function getFetchProxyScripts()
    {
        if (!isset(self::$_fetchProxyScripts)) {
            self::$_fetchProxyScripts = array();
            $m = Vps_Model_Abstract::getInstance('fetchProxyScripts');
            if ($m) {
                $s = new Vps_Model_Select();
                $s->whereEquals('active', true);
                $s->limit(5);
                $s->order(Vps_Model_Select::ORDER_RAND);
                foreach (Vps_Model_Abstract::getInstance($m)->export(Vps_Model_Interface::FORMAT_ARRAY, $s) as $r) {
                    self::$_fetchProxyScripts[] = array(
                        'id' => $r['id'],
                        'url' => $r['url'],
                        'version' => $r['version'],
                    );
                }
            }
        }
        return self::$_fetchProxyScripts;
    }

    public static function setFetchProxyScripts(array $fetchProxyScripts)
    {
        self::$_fetchProxyScripts = $fetchProxyScripts;
    }

    /**
     * @param array mit etag und lastmodified, kann false sein
     */
    public static function getRequestOptions($feed)
    {
        $options = Vps_Http_Requestor::getInstance()->getRequestOptions();
        if (isset($feed['etag'])) $options['etag'] = $feed['etag'];
        if (isset($feed['lastmodified'])) $options['lastmodified'] = $feed['lastmodified'];
        return $options;
    }

    /**
     * @param int|Vps_Util_FeedFetcher_FeedRow
     * @param float start microtime
     * @param Vps_Http_Requestor_Response_Interface response object
     */
    public static function handleResponse($feedId, $updateServer, $start, Vps_Http_Requestor_Response_Interface $response = null, &$error = false)
    {
        if (is_object($feedId)) {
            $row = $feedId;
        } else {
            $row = Vps_Model_Abstract::getInstance('feeds')->getRow($feedId);
        }

        ignore_user_abort(true);

        $cache = Vps_Util_FeedFetcher_Feed_Cache::getInstance();
        $cacheId = self::getCacheId($row->id);

        if ($response && $response->getStatusCode() == 304 && ($feed = $cache->load($cacheId))) {
            Vps_Benchmark::count('not-modified feed');
            $feed['update'] = time();
            $cache->save($feed, $cacheId);
            $status = self::UPDATE_NOT_MODIFIED;
        } else {
            $error = false;
            $options = array(
                'fetch_entries' => isset($row->fetch_entries) ? $row->fetch_entries : null,
            );
            $feed = self::getFeedDataFromResponse($row->id, $row->url, $options, $response, $error);
            if ($error) {
                $status = self::UPDATE_ERROR;
                if ($oldFeed = $cache->load($cacheId)) {
                    $feed = $oldFeed;
                    $feed['update'] = time();
                }
            } else {
                $status = self::UPDATE_SUCCESS_NO_NEW_ENTRIES;
                $oldFeed = $cache->load($cacheId);
                $oldIds = array();
                if ($oldFeed) {
                    foreach ($oldFeed['entries'] as $e) {
                        if (isset($e->id)) $oldIds[] = $e->id;
                    }
                }
                foreach ($feed['entries'] as $e) {
                    if (!in_array($e->id, $oldIds)) {
                        $status = self::UPDATE_SUCCESS_NEW_ENTRIES;
                        break;
                    }
                }
            }
            $cache->save($feed, $cacheId);
        }
        $duration = (microtime(true) - $start)*1000;

        self::updatedFeed($row, $feed, $updateServer, $duration, $status);

        return $feed;
    }
    public static function getFeedDataFromResponse($feedId, $url, array $options, Vps_Http_Requestor_Response_Interface $response, &$error)
    {
        $error = false;

        Vps_Http_Requestor::getInstance()->cacheResponse($url, $response);

        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds');
        $feed = array();
        $feed['url'] = $url;
        $feed['update'] = time();
        $entriesSelect = $m->select();
        if (isset($options['fetch_entries']) && $options['fetch_entries']) {
            $entriesSelect->limit($options['fetch_entries']);
        } else {
            $entriesSelect->limit(100);
        }
        try {
            if ($feedId > 40000) {
                $m->setDefaultEncoding('utf-8');
            } else {
                $m->setDefaultEncoding('iso-8859-1');
            }
            $f = $m->getRow($url);
            $feed['link'] = $f->link;
            $feed['title'] = $f->title;
            $feed['hub'] = $f->hub;
            $feed['source_encoding'] = $f->encoding;
            $feed['entries'] = array();
            foreach ($f->getChildRows('Entries', $entriesSelect) as $e) {
                $feed['entries'][] = (object)$e->toArray();
            }
        } catch(Exception $e) {
            $e = new Vps_Util_FeedFetcher_Exception_FeedUpdateOther($e);
            $e->log();
            $feed['entries'] = array();
            $feed['title'] = '';
            $feed['link'] = '';
            $feed['hub'] = null;
            $feed['source_encoding'] = 'utf-8';
            $error = true;
        }
        if (!$error && $response) {
            if ($response->getHeader('ETag')) {
	        $v = $response->getHeader('ETag');
	        if (is_array($v)) $v = $v[0];
                $feed['etag'] = $v;
            }
            if ($response->getHeader('Last-Modified')) {
	        $v = $response->getHeader('Last-Modified');
	        if (is_array($v)) $v = $v[0];
                $feed['lastmodified'] = strtotime($v);
            }
        }
        return $feed;
    }

    public static function updatedFeed(Vps_Util_FeedFetcher_FeedRow $row, array $feed, $updateServer, $duration, $status)
    {
        $row->last_update_fetch = date('Y-m-d H:i:s');

        $row->max_update = max($row->max_update, $duration);
        if (is_null($row->min_update)) $row->min_update = $duration;
        $row->min_update = min($row->min_update, $duration);
        if ($row->updates==0) {
            $row->avg_update = $duration;
        } else {
            $row->avg_update = ($row->avg_update*($row->updates) + $duration) / ($row->updates + 1);
        }
        $benchmarkType = false;
        $feedHost = parse_url($row->url, PHP_URL_HOST);
        if (substr($feedHost, -11) == 'twitter.com') {
            $benchmarkType = 'twitter';
        } else if (substr($feedHost, -10) == 'google.com') {
            $benchmarkType = 'google';
        } else if (substr($feedHost, -9, -2) == 'google.') {
            $benchmarkType = 'google';
        }
        if ($benchmarkType) Vps_Benchmark::count('feed-update-'.$benchmarkType);
        if ($status == self::UPDATE_ERROR) {
            $row->update_errors++;
            $row->last_update_error = date('Y-m-d H:i:s');
            $row->consecutive_update_errors++;
            Vps_Benchmark::count('feed-update-error');
            if ($benchmarkType) Vps_Benchmark::count('feed-error-'.$benchmarkType);
        } else {
            $row->consecutive_update_errors = 0;
            $row->last_successful_update = date('Y-m-d H:i:s');
            if ($status == self::UPDATE_SUCCESS_NEW_ENTRIES) {
                $row->last_update_fetch_new_entries = date('Y-m-d H:i:s');
            }
        }
        $cfg = Vps_Registry::get('config');
        if (isset($cfg->pubSubHubbub) && isset($cfg->pubSubHubbub->callbackUrl) && $cfg->pubSubHubbub->callbackUrl) {
            $cbUrl = 'http://'.Vps_Registry::get('config')->server->domain.$cfg->pubSubHubbub->callbackUrl;
            if (isset($feed['hub']) && $feed['hub'] && !$row->hub_subscribed) {
                $row->hub_subscribed = 'requested';
                $row->hub_url = $feed['hub'];
                $hubUrl = $feed['hub'];
                if (!preg_match("|^https?://|i",$hubUrl)) {
                    $hubUrl = parse_url($row->url, PHP_URL_SCHEME).'://'.parse_url($row->url, PHP_URL_HOST).$hubUrl;
                }
                $s = new Vps_Util_PubSubHubbub_Subscriber($hubUrl);
                $s->setCallbackUrl($cbUrl.'?feedId='.$row->id);
                $s->setVerifyToken($row->id);
                $row->save(); //hier erstmal speichern damit schon hub_subscribed gesetzt ist und damit der callback das auch schon sieht
                try {
                    $s->subscribe($row->url);
                } catch (Exception $e) {
                    $e = new Vps_Exception_Other($e);
                    $e->logOrThrow();
                    $row->hub_subscribed = 'failed';
                }
            } else if ((!isset($feed['hub']) || !$feed['hub']) && $row->hub_subscribed) {
                $row->hub_subscribed = null;
                $hubUrl = $row->hub_url;
                if (!preg_match("|^https?://|i",$hubUrl)) {
                    $hubUrl = parse_url($row->url, PHP_URL_SCHEME).'://'.parse_url($row->url, PHP_URL_HOST).$hubUrl;
                }
                $s = new Vps_Util_PubSubHubbub_Subscriber($hubUrl);
                $s->setCallbackUrl($cbUrl.'?feedId='.$row->id);
                $s->setVerifyToken($row->id);
                $row->save(); //hier erstmal speichern damit schon hub_subscribed gesetzt ist und damit der callback das auch schon sieht
                try {
                    $s->unsubscribe($row->url);
                } catch (Exception $e) {
                    $e = new Vps_Exception_Other($e);
                    $e->logOrThrow();
                }
            }
        }
        $row->updated($updateServer, $status);

        if (isset($row->log_activated) && $row->log_activated) {
            //TODO ist auch in Vps_Rssinclude_PubSubHubbub::process
            $log = $row->createChildRow('UpdateLog');
            $log->date = date('Y-m-d H:i:s');
            $log->server = $updateServer;
            $log->duration = $duration;
            $log->status = $status;

            $cache = Vps_Util_FeedFetcher_Feed_Cache::getInstance();
            $cacheId = self::getCacheId($row->id);
            if ($data = $cache->load($cacheId)) {
                $log->entries = count($data['entries']);
                $titles = array();
                foreach ($data['entries'] as $e) {
                    $t = $e->title;
                    if (!$t) $t = $e->url;
                    if (strlen($t) > 50) $t = substr($t, 0, 50);
                    $titles[] = $e->id.' '.$t;
                }
                $log->titles = implode('; ', $titles);;
            }
            $log->save();
        }

        $row->save();
    }

    /**
     * @return Vps_Util_Model_Feed_Row_Feed
     */
    public static function getFeed($feedId, $updateServer = null)
    {
        if (!$updateServer) $updateServer = Vps_Benchmark::getUrlType();
        $log = date('Y-m-d H:i:s').' '.$updateServer.' '.$feedId.' ';
        $cache = Vps_Util_FeedFetcher_Feed_Cache::getInstance();
        $cacheId = self::getCacheId($feedId);

        $feed = Vps_Util_FeedFetcher_Feed_Cache::getInstance()->load($cacheId);
        if ($feed) {
            if (!isset($feed['update']) || time()-$feed['update'] > 25*60*60) {
                $feed = false;
            }
        }
        if (!$feed) {
            $log .= "uncached ";
            $row = Vps_Model_Abstract::getInstance('feeds')->getRow($feedId);
            $log .= $row->last_update;
            $request = self::createRequest($feedId);
            $start = microtime(true);
            try {
                $response = $request->send();
            } catch(Exception $e) {
                $e = new Vps_Util_FeedFetcher_Exception_FeedUpdateOther($e);
                $e->log();
                $response = null;
            }
            $response = new Vps_Http_Pecl_Requestor_Response($response);
            $feed = self::handleResponse($feedId, $updateServer, $start, $response);

            file_put_contents("application/log/get-feed", $log."\n", FILE_APPEND);

        } else {
            $log .= "cached";
        }
        return $feed;
    }

    public static function getCacheId($feedId)
    {
        return 'feed'.$feedId;
    }
}
