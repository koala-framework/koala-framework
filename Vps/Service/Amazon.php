<?php
class Vps_Service_Amazon extends Zend_Service_Amazon
{
    public function __construct($appId = '0CJ03620WGKVWMR2F3R2', $countryCode = 'DE')
    {
        parent::__construct($appId, $countryCode);
    }

    /**
     * Search for Items
     *
     * @param  array $options Options to use for the Search Query
     * @throws Zend_Service_Exception
     * @return Vps_Service_Amazon_ResultSet
     * @see http://www.amazon.com/gp/aws/sdk/main.html/102-9041115-9057709?s=AWSEcommerceService&v=2005-10-05&p=ApiReference/ItemSearchOperation
     */
    public function itemSearch(array $options)
    {
        Vps_Benchmark::countBt('Service Amazon request', 'itemSearch'.print_r($options, true));
        $defaultOptions = array('ResponseGroup' => 'Small');
        $options = $this->_prepareOptions('ItemSearch', $options, $defaultOptions);
        $this->_rest->getHttpClient()->resetParameters();
        $response = $this->_rest->restGet('/onca/xml', $options);

        if ($response->isError()) {
            throw new Zend_Service_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);

        return new Vps_Service_Amazon_ResultSet($dom);
    }


    /**
     * Look up item(s) by ASIN
     *
     * @param  string $asin    Amazon ASIN ID
     * @param  array  $options Query Options
     * @see http://www.amazon.com/gp/aws/sdk/main.html/102-9041115-9057709?s=AWSEcommerceService&v=2005-10-05&p=ApiReference/ItemLookupOperation
     * @throws Zend_Service_Exception
     * @return Vps_Service_Amazon_Item|Vps_Service_Amazon_ResultSet
     */
    public function itemLookup($asin, array $options = array())
    {
        Vps_Benchmark::count('Service Amazon request', 'itemLookup '.$asin);

        $defaultOptions = array('IdType' => 'ASIN', 'ResponseGroup' => 'Small');
        $options['ItemId'] = (string) $asin;
        $options = $this->_prepareOptions('ItemLookup', $options, $defaultOptions);
        $this->_rest->getHttpClient()->resetParameters();
        $response = $this->_rest->restGet('/onca/xml', $options);

        if ($response->isError()) {
            /**
             * @see Zend_Service_Exception
             */
            require_once 'Zend/Service/Exception.php';
            throw new Zend_Service_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2005-10-05');
        $items = $xpath->query('//az:Items/az:Item');

        if ($items->length == 1) {
            return new Vps_Service_Amazon_Item($items->item(0));
        }

        return new Vps_Service_Amazon_ResultSet($dom);
    }

    /**
     * Look up item(s) by ASIN
     *
     * @param  string $asin    Amazon ASIN ID
     * @param  array  $options Query Options
     * @see http://docs.amazonwebservices.com/AWSEcommerceService/2005-10-05/ApiReference/BrowseNodeLookupOperation.html
     * @throws Zend_Service_Exception
     * @return Vps_Service_Amazon_BrowseNode
     */
    public function browseNodeLookup($nodeId, array $options = array())
    {
        Vps_Benchmark::count('Service Amazon request', 'browseNodeLookup');

        $defaultOptions = array('IdType' => 'ASIN', 'ResponseGroup' => 'BrowseNodeInfo');
        $options['BrowseNodeId'] = (string) $nodeId;
        $options = $this->_prepareOptions('BrowseNodeLookup', $options, $defaultOptions);
        $this->_rest->getHttpClient()->resetParameters();
        $response = $this->_rest->restGet('/onca/xml', $options);

        if ($response->isError()) {
            /**
             * @see Zend_Service_Exception
             */
            require_once 'Zend/Service/Exception.php';
            throw new Zend_Service_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);
        return new Vps_Service_Amazon_BrowseNode($dom);
    }
}
