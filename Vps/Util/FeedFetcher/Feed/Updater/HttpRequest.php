<?php
class Vps_Util_FeedFetcher_Feed_Updater_HttpRequest extends HttpRequest
{
    private $_feed;
    private $_start;

    public function __construct(Vps_Util_FeedFetcher_FeedRow $feed)
    {
        $options = Vps_Util_FeedFetcher_Feed::getRequestOptions($feed->id);
        parent::__construct($feed->url, HTTP_METH_GET, $options);
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
