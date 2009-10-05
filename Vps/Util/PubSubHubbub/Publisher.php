<?php
// a PHP client library for pubsubhubbub
// as defined at http://code.google.com/p/pubsubhubbub/
// written by Josh Fraser | joshfraser.com | josh@eventvue.com
// Released under Apache License 2.0

class Vps_Util_PubSubHubbub_Publisher
{
    protected $_hubUrl;

    public function __construct($hubUrl)
    {
        if (!isset($hubUrl))
            throw new Exception('Please specify a hub url');

        if (!preg_match("|^https?://|i",$hubUrl))
            throw new Exception('The specified hub url does not appear to be valid: '.$hubUrl);

        $this->_hubUrl = $hubUrl;
    }

    public function publishUpdate($topicUrls)
    {
        if (!isset($topicUrls))
            throw new Vps_Exception('Please specify a topic url');

        if (!is_array($topicUrls)) $topicUrls = array($topicUrls);


        $client = new Zend_Http_Client($this->_hubUrl);
        $client->setConfig(array(
            'timeout' => 60,
            'persistent' => true
        ));
        $client->setMethod(Zend_Http_Client::POST);
        $data = array(
            'hub.mode' => 'publish',
        );
        $client->setParameterPost($data);
        foreach ($topicUrls as $u) {
            if (!preg_match("|^https?://|i",$u))
                throw new Vps_Exception('The specified topic url does not appear to be valid: '.$topicUrl);
            $client->setParameterPost('hub.url', $u);
        }
        $response = $client->request();
        if ($response->isError()) {
            throw new Vps_Exception("publishUpdate failed, response status '{$response->getStatus()}' '{$response->getBody()}'");
        }
        return $response;
    }
}
