<?php
class Kwf_Component_Generator_Plugin_StatusUpdate_Backend_Twitter extends Kwf_Component_Generator_Plugin_StatusUpdate_Backend_Abstract
{
    protected $_type = 'twitter';

    private $_config = array(
        'signatureMethod' => 'HMAC-SHA1',
        'requestTokenUrl' => 'https://api.twitter.com/oauth/request_token',
        'authorizeUrl' => 'https://api.twitter.com/oauth/authorize',
        'accessTokenUrl' => 'https://api.twitter.com/oauth/access_token',
        'consumerKey' => 'tSN8Mu8OGbcpSgYPEpHqMA',
        'consumerSecret' => 'xXD9QuwIwkPiJsaPbkH2kiuBxNOZJ7JDNTU79z19c'
    );

    public function __construct($callbackUrl)
    {
        $this->_config['callbackUrl'] = $callbackUrl;
    }

    private function _getOauthConsumer()
    {
        return new Zend_Oauth_Consumer($this->_config);
    }

    public function getAuthUrl()
    {
        if ($this->isAuthed()) throw new Kwf_Exception("already authed");

        $consumer = $this->_getOauthConsumer();
        $requestToken = $consumer->getRequestToken();
        $session = new Kwf_Session_Namespace('statusUpdate_OAuth');
        $session->requestToken = $requestToken;
        return $consumer->getRedirectUrl();
    }

    public function processCallback($queryData)
    {
        $session = new Kwf_Session_Namespace('statusUpdate_OAuth');
        if (!isset($session->requestToken)) {
            throw new Kwf_Exception("requestToken not in session");
        }
        $requestToken = $session->requestToken;

        $consumer = $this->_getOauthConsumer();
        $accessToken = $consumer->getAccessToken($queryData, $requestToken);
        if (!$accessToken) {
            throw new Kwf_Exception("getting access token failed");
        }
        $this->_getAuthRow()->auth_token = serialize($accessToken);
        $this->_getAuthRow()->save();
    }

    public function send($message, $logRow)
    {
        if (!$this->_getAuthRow()->auth_token) {
            throw new Kwf_Exception('no auth token saved');
        }

        $accessToken = unserialize($this->_getAuthRow()->auth_token);
        Zend_Service_Twitter::setHttpClient($accessToken->getHttpClient($this->_config));

        $twitter = new Zend_Service_Twitter(null, null);
        $response = $twitter->account->verifyCredentials();
        if (!$response->isSuccess()) {
            throw new Kwf_Exception('verifyCredentials failed: '.$response->__toString());
        }
        $response = $twitter->statusUpdate($message);
        if (!$response->isSuccess()) {
            throw new Kwf_Exception('statusUpdate failed: '.$response->__toString());
        }

        $logRow->status_id = (string)$response->id;
        $logRow->user_id = (string)$response->user->id;
        $logRow->screen_name = (string)$response->user->screen_name;
    }

    public function getName() { return 'Twitter'; }
}
