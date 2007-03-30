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
            if(!isset($this->_productId)) {
                $this->_productId = substr($this->getPageKey(), strpos($this->getPageKey(), '.')+1);
            }
            $products = $dao->getTable('E3_Dao_ProductProducts')->find($this->_productId);
            //fixme: raise error?
            $this->_product = $products->current();
        }
        return $this->_product;
    }

    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);

        $product = $this->getProduct();
        $ret['name'] = $product->name;
        $ret['filename'] = $product->filename;
        $ret['price'] = $product->price;
        $ret['vat'] = $product->vat;

        $ret['content'] = $this->_getContentComponent()->getTemplateVars($mode);

        if ($mode == 'edit') {
            $ret['template'] = dirname(__FILE__).'/Details.html';
        } else {
             $ret['template'] = 'Product/Details.html';
        }

        return $ret;
    }
    private function _getContentComponent()
    {
        if (!isset($this->_content)) {
            $product = $this->getProduct();
            $componentClass = $this->getDao()->getTable('E3_Dao_Components')
                                ->getComponentClass($product->component_id);
            $this->_content = new $componentClass($this->getDao(), $product->component_id);
        }
        return $this->_content;
    }
    public function getComponentInfo()
    {
    	return parent::getComponentInfo() + $this->_content->getComponentInfo();
    }
    public function saveFrontendEditing(Zend_Controller_Request_Http $request)
    {
        $product = $this->getProduct();
        $product->filename = $request->getPost('filename');
        $product->name = $request->getPost('name');
        $product->price = $request->getPost('price');
        $product->vat = $request->getPost('vat');
        $product->save();

        $ret = parent::saveFrontendEditing($request);
        $ret['createComponents'] = $this->_getContentComponent()->getComponentInfo();
        return $ret;
    }
}
