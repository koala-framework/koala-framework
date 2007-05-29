<?php
class Vpc_Product_Teaser extends Vpc_TextPic
{
    private $_productData;

    public function setProductData($data)
    {
        $this->_productData = $data;
    }
    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);
        $ret['product'] = $this->_productData;
        return $ret;
    }
}