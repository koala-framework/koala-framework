<?php
class Kwc_Advanced_Amazon_Product_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Amazon.Product');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';

        $ret['associateTag'] = 'kwf-21';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);

        static $productsByPage = array();
        $products = array();

        if (!isset($productsByPage[$this->getData()->parent->componentId])) {
            $all = $this->getData()->parent->getChildComponents(array(
                'componentClass' => $this->getData()->componentClass,
                'ignoreVisible' => true
            ));
            $asins = array();
            foreach ($all as $c) {
                $asins[] = $c->getComponent()->getRow()->asin;
            }
            $asinsChunks = array_chunk($asins, 10);
            foreach ($asinsChunks as $chunk) {
                $amazon = new Kwf_Service_Amazon();
                try {
                    $resultSet = $amazon->itemLookup(
                        implode($chunk,','), 
                        array(
                            'AssociateTag' => $this->_getSetting('associateTag'),
                            'ResponseGroup' => 'Small,ItemAttributes,Images'
                        )
                    );
                } catch (Zend_Service_Exception $e) {
                    $e = new Kwf_Exception_Other($e);
                    $e->logOrThrow();
                    $resultSet = array();
                }
                if ($resultSet instanceof Kwf_Service_Amazon_Item) {
                    $resultSet = array($resultSet);
                }
                foreach ($resultSet as $i) {
                    if (!is_null($i) && isset($i->ASIN)) {
                        $products[$i->ASIN] = (object)array(
                            'item' => $i,
                            'title' => isset($i->Title) ? $i->Title : null,
                            'author' => isset($i->Author) ? (is_array($i->Author) ? implode($i->Author, ', ') : $i->Author) : null,
                            'asin' => $i->ASIN, 
                            'detailPageURL' => isset($i->DetailPageURL) ? $i->DetailPageURL : null,
                            'currencyCode' => isset($i->CurrencyCode) ? $i->CurrencyCode : null, 
                            'amount' => isset($i->Amount) ? $i->Amount : null, 
                            'formattedPrice' => isset($i->FormattedPrice) ? $i->FormattedPrice : null,
                            'salesRank' => isset($i->SalesRank) ? $i->SalesRank : null, 
                            'averageRating' => isset($i->AverageRating) ? $i->AverageRating : null
                        );
                    }
                }
            }

            $productsByPage[$this->getData()->parent->componentId] = $products;
        }

        $ret['product'] = null;
        if ($this->getRow()->asin) {
            if (isset($productsByPage[$this->getData()->parent->componentId][strtoupper($this->getRow()->asin)])) {
                $ret['product'] = $productsByPage[$this->getData()->parent->componentId][strtoupper($this->getRow()->asin)];
            }
        }

        return $ret;
    }

    public function getViewCacheLifetime()
    {
        return 24*60*60;
    }
}
