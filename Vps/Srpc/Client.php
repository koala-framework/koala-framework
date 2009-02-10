<?php
/**
 * Client for Serialized RPC
 */
class Vps_Srpc_Client
{
    protected $_serverUrl;
    protected $_extraParams = array();

    public function __construct(array $config = array())
    {
        if (!empty($config['serverUrl'])) {
            $this->setServerUrl($config['serverUrl']);
        }
        if (!empty($config['extraParams']) && is_array($config['extraParams'])) {
            if (array_key_exists('method', $config['extraParams'])
                || array_key_exists('arguments', $config['extraParams'])
            ) {
                throw new Vps_Exception("'method' or 'argument' may not be a key of config value 'extraParams'");
            }
            $this->_extraParams = $config['extraParams'];
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
            'arguments' => array(),
            'extraParams' => array()
        );
        if (is_array($args) && count($args)) {
            $params['arguments'] = $args;
        }

        if ($this->_extraParams) {
            $params['extraParams'] = $this->_extraParams;
        }
        $params['arguments'] = serialize($params['arguments']);
        $params['extraParams'] = serialize($params['extraParams']);

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

