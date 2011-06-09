<?php
/**
 * Client for Serialized RPC
 */
class Vps_Srpc_Client
{
    protected $_serverUrl;
    protected $_extraParams = array();
    protected $_timeout = 20; // standard von Zend ist 10
    protected $_proxy = array();

    public function __construct(array $config = array())
    {
        if (!empty($config['serverUrl'])) {
            $this->setServerUrl($config['serverUrl']);
        }
        if (!empty($config['proxyHost'])) {
            $this->_proxy['proxy_host'] = $config['proxyHost'];
        }
        if (!empty($config['proxyPort'])) {
            $this->_proxy['proxy_port'] = $config['proxyPort'];
        }
        if (!empty($config['proxyUser'])) {
            $this->_proxy['proxy_user'] = $config['proxyUser'];
        }
        if (!empty($config['proxyPassword'])) {
            $this->_proxy['proxy_pass'] = $config['proxyPassword'];
        }
        if (!empty($config['extraParams']) && is_array($config['extraParams'])) {
            if (array_key_exists('method', $config['extraParams'])
                || array_key_exists('arguments', $config['extraParams'])
            ) {
                throw new Vps_Exception("'method' or 'argument' may not be a key of config value 'extraParams'");
            }
            $this->_extraParams = $config['extraParams'];
        }
        if (!empty($config['timeout']) && is_integer($config['timeout'])) {
            $this->setTimeout($config['timeout']);
        }
    }

    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
        return $this;
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
        $httpClientConfig = array(
            'timeout' => $this->_timeout,
            'persistent' => true
        );
        if ($this->_proxy) {
            $httpClientConfig = array_merge($httpClientConfig, $this->_proxy);
            $httpClientConfig['adapter'] = 'Zend_Http_Client_Adapter_Proxy';
        }
        $httpClient = new Zend_Http_Client($this->getServerUrl(), $httpClientConfig);
        $httpClient->setMethod(Zend_Http_Client::POST);
        $httpClient->setParameterPost($params);
        $log = "\n\n\n".date('Y-m-d H:i:s')." ".$this->getServerUrl()."\nCONFIG: ".print_r($httpClientConfig, true)."\nPARAMS: ".print_r($params, true);
        file_put_contents('application/log/srpc-request-values', $log, FILE_APPEND);
        return $httpClient->request()->getBody();
    }

    public function __call($method, $args)
    {
        $log = date('Y-m-d H:i:s')." (start) $this->_serverUrl $method ".(isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : '?')."\n";
        file_put_contents('application/log/srpc-call', $log, FILE_APPEND);
        $start = microtime(true);
        $b = Vps_Benchmark::start('srpc call', $this->_serverUrl.' '.$method);

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

        $log = date('Y-m-d H:i:s').' '.round(microtime(true)-$start, 2)."s $this->_serverUrl $method ".(isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : '?')."\n";
        file_put_contents('application/log/srpc-call', $log, FILE_APPEND);
        if ($b) $b->stop();

        try {
            $result = unserialize($response);
        } catch (Exception $e) {
            throw new Vps_Exception('Srpc Server Response is not serialized: '.$response);
        }
        if ($result === false) {
            throw new Vps_Exception('Srpc Server Response is not serialized: '.$response);
        }

        // result könnte eine Exception sein, wenn ja wird sie weitergeschmissen
        if ($result instanceof Vps_Exception_Serializable) {
            throw $result->getException();
        } else if ($result instanceof Exception) {
            throw $result;
        }

        return $result;
    }
}
