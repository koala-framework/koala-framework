<?php
// a PHP client library for pubsubhubbub
// as defined at http://code.google.com/p/pubsubhubbub/
// written by Josh Fraser | joshfraser.com | josh@eventvue.com
// Released under Apache License 2.0

class Vps_Util_PubSubHubbub_Subscriber
{
    protected $_hubUrl;
    protected $_callbackUrl;
    protected $_credentials;
    protected $_verifyToken = '';

    public function __construct($hubUrl, $credentials = false)
    {
        if (!isset($hubUrl))
            throw new Exception('Please specify a hub url');

        if (!preg_match("|^https?://|i",$hubUrl))
            throw new Exception('The specified hub url does not appear to be valid: '.$hubUrl);

        $this->_hubUrl = $hubUrl;

        $this->_callbackUrl = 'http://'.Vps_Registry::get('config')->server->domain.'/pshb_cb';
        $this->_credentials = $credentials;
    }

    public function setCallbackUrl($url)
    {
        $this->_callbackUrl = $url;
    }
    public function setVerifyToken($token)
    {
        $this->_verifyToken = $token;
    }

    public function subscribe($topicUrl)
    {
        return $this->_changeSubscription('subscribe', $topicUrl);
    }

    public function unsubscribe($topicUrl)
    {
        return $this->_changeSubscription('unsubscribe', $topicUrl);
    }

    private function _changeSubscription($mode, $topicUrl)
    {
        if (!isset($topicUrl))
            throw new Vps_Exception('Please specify a topic url');

         if (!preg_match("|^https?://|i",$topicUrl))
            throw new Vps_Exception('The specified topic url does not appear to be valid: '.$topicUrl);

        // set the mode subscribe/unsubscribe
        $data = array(
            'hub.mode' => $mode,
            'hub.callback' => $this->_callbackUrl,
            'hub.verify' => 'async', //"async" or "sync"
            'hub.verify_token' => $this->_verifyToken,
            //'hub.leaseSeconds' => $this->_leaseSeconds,
            'hub.topic' => $topicUrl,
        );
        $client = new Zend_Http_Client($this->_hubUrl);
        $client->setConfig(array(
            'timeout' => 60,
            'persistent' => true
        ));
        $client->setMethod(Zend_Http_Client::POST);
        $client->setParameterPost($data);
        $response = $client->request();
        if ($response->isError()) {
            throw new Vps_Exception("$mode failed, response status '{$response->getStatus()}' '{$response->getBody()}'");
        }
        return $response;
    }
}
