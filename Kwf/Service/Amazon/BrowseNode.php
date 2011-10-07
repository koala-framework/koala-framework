<?php
class Vps_Service_Amazon_BrowseNode
{
    protected $_dom;
    /**
     * Parse the given <Item> element
     *
     * @param  DOMElement $dom
     * @return void
     */
    public function __construct(DOMDocument $dom)
    {
/*
        $this->_xpath = new DOMXPath($dom);
        $this->_xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2005-10-05');
        $this->_results = $this->_xpath->query('//az:Item');

    */
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2005-10-05');
        $this->BrowseNodeId = $xpath->query('//az:BrowseNode/az:BrowseNodeId/text()', $dom)->item(0)->data;
        $this->Name = $xpath->query('//az:BrowseNode/az:Name/text()', $dom)->item(0)->data;

        $this->_dom = $dom;
    }


    /**
     * Returns the item's original XML
     *
     * @return string
     */
    public function asXml()
    {
        return $this->_dom->ownerDocument->saveXML($this->_dom);
    }
}
