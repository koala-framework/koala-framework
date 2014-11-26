<?php
class Kwf_Service_Amazon extends Zend_Service_Amazon
{
    public function __construct($appId = null, $countryCode = 'DE', $secretKey = null)
    {
        if (!$appId || !$secretKey) {
            $a = Kwf_Registry::get('config')->amazon;
            if (!$a) throw new Kwf_Exception("Set amazon.key and amazon.secret in config");
            if (!$appId) $appId = $a->key;
            if (!$secretKey) $secretKey = $a->secret;
        }
        parent::__construct($appId, $countryCode, $secretKey);
    }

    /**
     * Search for Items
     *
     * @param  array $options Options to use for the Search Query
     * @throws Zend_Service_Exception
     * @return Kwf_Service_Amazon_ResultSet
     * @see http://www.amazon.com/gp/aws/sdk/main.html/102-9041115-9057709?s=AWSEcommerceService&v=2011-08-01&p=ApiReference/ItemSearchOperation
     */
    public function itemSearch(array $options)
    {
        Kwf_Benchmark::countBt('Service Amazon request', 'itemSearch'.print_r($options, true));

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('ResponseGroup' => 'Small');
        $options = $this->_prepareOptions('ItemSearch', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        $response = $client->restGet('/onca/xml', $options);

        if ($response->isError()) {
            /**
             * @see Zend_Service_Exception
             */
            throw new Zend_Service_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);

        return new Kwf_Service_Amazon_ResultSet($dom);
    }


    /**
     * Look up item(s) by ASIN
     *
     * @param  string $asin    Amazon ASIN ID
     * @param  array  $options Query Options
     * @see http://www.amazon.com/gp/aws/sdk/main.html/102-9041115-9057709?s=AWSEcommerceService&v=2011-08-01&p=ApiReference/ItemLookupOperation
     * @throws Zend_Service_Exception
     * @return Kwf_Service_Amazon_Item|Kwf_Service_Amazon_ResultSet
     */
    public function itemLookup($asin, array $options = array())
    {
        Kwf_Benchmark::count('Service Amazon request', 'itemLookup '.$asin);

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);
        $client->getHttpClient()->resetParameters();

        $defaultOptions = array('ResponseGroup' => 'Small');
        $options['ItemId'] = (string) $asin;
        $options = $this->_prepareOptions('ItemLookup', $options, $defaultOptions);
        $response = $client->restGet('/onca/xml', $options);

        if ($response->isError()) {
            /**
             * @see Zend_Service_Exception
             */
            throw new Zend_Service_Exception(
                'An error occurred sending request. Status code: ' . $response->getStatus()
            );
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2011-08-01');
        $items = $xpath->query('//az:Items/az:Item');

        if ($items->length == 1) {
            return new Kwf_Service_Amazon_Item($items->item(0));
        }

        return new Kwf_Service_Amazon_ResultSet($dom);
    }

    /**
     * Look up item(s) by ASIN
     *
     * @param  string $asin    Amazon ASIN ID
     * @param  array  $options Query Options
     * @see http://docs.amazonwebservices.com/AWSEcommerceService/2011-08-01/ApiReference/BrowseNodeLookupOperation.html
     * @throws Zend_Service_Exception
     * @return Kwf_Service_Amazon_BrowseNode
     */
    public function browseNodeLookup($nodeId, array $options = array())
    {
        Kwf_Benchmark::count('Service Amazon request', 'browseNodeLookup');

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);
        $client->getHttpClient()->resetParameters();

        $defaultOptions = array('IdType' => 'ASIN', 'ResponseGroup' => 'BrowseNodeInfo');
        $options['BrowseNodeId'] = (string) $nodeId;
        $options = $this->_prepareOptions('BrowseNodeLookup', $options, $defaultOptions);
        $response = $client->restGet('/onca/xml', $options);

        if ($response->isError()) {
            /**
             * @see Zend_Service_Exception
             */
            throw new Zend_Service_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);
        return new Kwf_Service_Amazon_BrowseNode($dom);
    }
}
