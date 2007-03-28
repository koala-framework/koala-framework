<?php
class E3_Component_Product_Details extends E3_Component_Abstract
{
    private $_product;
    private $_productId;
    private $_content;
    public function setProductId($id)
    {
        $this->_productId = $id;
    }

    private function getProduct()
    {
        if (!isset($this->_product)) {
            $dao = $this->getDao();
            $products = $dao->getTable('E3_Dao_ProductProducts')->find($this->_productId);
            //fixme: raise error?
            $this->_product = $products->current();
        }
        return $this->_product;
    }

    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);

        $componentModel = $this->getDao()->getTable('E3_Dao_Components');

        $product = $this->getProduct();
        $ret['name'] = $product->name;
        $ret['filename'] = $product->filename;
        $ret['price'] = $product->price;
        $ret['vat'] = $product->vat;

        $componentClass = $componentModel->getComponentClass($product->component_id);
        $this->_content = new $componentClass($this->getDao(), $product->component_id);
        $ret['content'] = $this->_content->getTemplateVars($mode);

       	$ret['template'] = 'Product/Details.html';
        return $ret;
    }
    public function getComponentInfo()
    {
    	return parent::getComponentInfo() + $this->_content->getComponentInfo();
    }
}
