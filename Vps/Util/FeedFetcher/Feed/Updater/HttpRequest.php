<?php
class Vps_Util_FeedFetcher_Feed_Updater_HttpRequest extends HttpRequest
{
    private $_feed;
    private $_start;

    public function __construct(Vps_Util_FeedFetcher_FeedRow $feed)
    {
        $feedData = Vps_Util_FeedFetcher_Feed_Cache::getInstance()
            ->load(Vps_Util_FeedFetcher_Feed::getCacheId($feed->id));

        $options = Vps_Util_FeedFetcher_Feed::getRequestOptions($feedData);

        $url = Vps_Util_FeedFetcher_Feed::getRequestUrl($feedId, $feed->url);
        parent::__construct($url, HTTP_METH_GET, $options);
        $this->_feed = $feed;

        $this->_start = microtime(true);
    }

    public function getFeed()
    {
        return $this->_feed;
    }

    public function getStart()
    {
        return $this->_start;
    }
}
