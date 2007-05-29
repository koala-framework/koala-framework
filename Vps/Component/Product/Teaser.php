<?php
class Vps_Component_Product_Teaser extends Vps_Component_TextPic
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