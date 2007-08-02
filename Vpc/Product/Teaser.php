<?php
class Vpc_Product_Teaser extends Vpc_Abstract
{
    private $_productData;

    public function setProductData($data)
    {
        $this->_productData = $data;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['product'] = $this->_productData;
        return $ret;
    }
}