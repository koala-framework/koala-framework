<?php
/**
 * Client for Serialized RPC
 */
class Vps_Srpc_Client
{
    protected $_serverUrl;

    public function __construct(array $config = array())
    {
        if (!empty($config['serverUrl'])) {
            $this->setServerUrl($config['serverUrl']);
        }
    }

    public function setServerUrl($serverUrl)
    {
        $this->_serverUrl = $serverUrl;
    }

    public function getServerUrl()
    {
        if (!$this->_serverUrl) {
            throw new Vps_Exception('serverUrl for Vps_Srpc_Client has not been set.');
        }
        return $this->_serverUrl;
    }

    protected function _performRequest(array $params)
    {
        $httpClient = new Zend_Http_Client($this->getServerUrl());
        $httpClient->setMethod(Zend_Http_Client::POST);
        $httpClient->setParameterPost($params);
        return $httpClient->request()->getBody();
    }

    public function __call($method, $args)
    {
        $params = array(
            'method' => $method,
            'arguments' => array()
        );
        if (is_array($args) && count($args)) {
            $params['arguments'] = $args;
        }

        $params['arguments'] = serialize($params['arguments']);

        $response = $this->_performRequest($params);
        if (@unserialize($response) === false) {
            throw new Vps_Exception('Srpc Server Response is not serialized: '.$response);
        } else {
            $result = unserialize($response);
        }

        // result könnte eine Exception sein, wenn ja wird sie weitergeschmissen
        if ($result instanceof Exception) {
            throw $result;
        }

        return $result;
    }
}

