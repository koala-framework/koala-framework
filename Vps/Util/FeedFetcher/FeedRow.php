<?php
class Vps_Util_FeedFetcher_FeedRow extends Vps_Model_Proxy_Row
{
    public function getFeed()
    {
        return Vps_Util_FeedFetcher_Feed::getFeed($this->id);
    }
    public function updated($updateServer, $status)
    {
        $this->last_update = date('Y-m-d H:i:s');
        $this->updates++;
        $this->last_update_server = $updateServer;
        if ($status != Vps_Util_FeedFetcher_Feed::UPDATE_ERROR) {
            $this->consecutive_update_errors = 0;
            $this->last_successful_update = date('Y-m-d H:i:s');
        }
    }
}
