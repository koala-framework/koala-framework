<?php
class E3_Component_Product_Teaser extends E3_Component_TextPic
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