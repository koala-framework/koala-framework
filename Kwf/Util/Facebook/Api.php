<?php
class Kwf_Util_Facebook_Api extends Kwf_Util_Facebook_FacebookZendSession
{
    static private $instance = null;

    static public function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct()
    {
        $config = Kwf_Config::getValueArray('kwc.fbAppData');
        if (!isset($config['appId'])) {
            throw new Kwf_Exception('kwc.fbAppData.appId has to be set in config');
        }
        if (!isset($config['secret'])) {
            throw new Kwf_Exception('kwc.fbAppData.secret has to be set in config');
        }
        $fbConfig['appId'] = $config['appId'];
        $fbConfig['secret'] = $config['secret'];
        parent::__construct($fbConfig);
    }
    private function __clone(){}

    //has to be overridden because if you want to make a request through a proxy
    //proxy can be set in config
    protected function makeRequest($url, $params, $ch=null) {
        if (!$ch) {
        $ch = curl_init();
        }

        $opts = self::$CURL_OPTS;
        $configProxy = Kwf_Config::getValueArray('http.proxy');
        if (isset($configProxy['host']) && $configProxy['host']) {
            $opts[CURLOPT_PROXY] = $configProxy['host'];
            $opts[CURLOPT_SSL_VERIFYPEER] = false;
        }
        if (isset($configProxy['port']) && $configProxy['port']) {
            $opts[CURLOPT_PROXYPORT] = $configProxy['port'];
        }
        if ($this->getFileUploadSupport()) {
        $opts[CURLOPT_POSTFIELDS] = $params;
        } else {
        $opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
        }
        $opts[CURLOPT_URL] = $url;
        // disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
        // for 2 seconds if the server does not support this header.
        if (isset($opts[CURLOPT_HTTPHEADER])) {
        $existing_headers = $opts[CURLOPT_HTTPHEADER];
        $existing_headers[] = 'Expect:';
        $opts[CURLOPT_HTTPHEADER] = $existing_headers;
        } else {
        $opts[CURLOPT_HTTPHEADER] = array('Expect:');
        }

        curl_setopt_array($ch, $opts);
        $result = curl_exec($ch);

        if (curl_errno($ch) == 60) { // CURLE_SSL_CACERT
        self::errorLog('Invalid or no certificate authority found, '.
                        'using bundled information');
        curl_setopt($ch, CURLOPT_CAINFO,
                    dirname(__FILE__) . '/fb_ca_chain_bundle.crt');
        $result = curl_exec($ch);
        }

        // With dual stacked DNS responses, it's possible for a server to
        // have IPv6 enabled but not have IPv6 connectivity.  If this is
        // the case, curl will try IPv4 first and if that fails, then it will
        // fall back to IPv6 and the error EHOSTUNREACH is returned by the
        // operating system.
        if ($result === false && isset(CURLOPT_IPRESOLVE) && empty($opts[CURLOPT_IPRESOLVE])) {
            $matches = array();
            $regex = '/Failed to connect to ([^:].*): Network is unreachable/';
            if (preg_match($regex, curl_error($ch), $matches)) {
            if (strlen(@inet_pton($matches[1])) === 16) {
                self::errorLog('Invalid IPv6 configuration on server, '.
                            'Please disable or get native IPv6 on your server.');
                self::$CURL_OPTS[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                $result = curl_exec($ch);
            }
            }
        }

        if ($result === false) {
        $e = new FacebookApiException(array(
            'error_code' => curl_errno($ch),
            'error' => array(
            'message' => curl_error($ch),
            'type' => 'CurlException',
            ),
        ));
        curl_close($ch);
        throw $e;
        }
        curl_close($ch);
        return $result;
    }
}
