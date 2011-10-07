<?php
class Vps_Service_Amazon_Item extends Zend_Service_Amazon_Item
{
    public $BrowseNodes;

    public function __construct(DOMElement $dom)
    {
        parent::__construct($dom);

        $xpath = new DOMXPath($dom->ownerDocument);
        $xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2005-10-05');
        $result = $xpath->query('./az:BrowseNodes/az:BrowseNode', $dom);
        if ($result->length >= 1) {
            $this->BrowseNodes = array();
            foreach ($result as $v) {
                $r = $xpath->query('./az:BrowseNodeId/text()', $v);
                $this->BrowseNodes[] = (string)$r->item(0)->data;
            }
        }
    }
}
