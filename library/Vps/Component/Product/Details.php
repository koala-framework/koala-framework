<?php
class Vps_Component_Product_Details extends Vps_Component_Abstract
{
    private $_productId;
    private $_product;
    private $_content;
    
    public function setProductId($id)
    {
        $this->_productId = $id;
    }
    
    protected function setup()
    {
        $dao = $this->getDao();
        if (!isset($this->_productId)) {
            $this->_productId = substr($this->getPageKey(), strpos($this->getPageKey(), '.') + 1);
        }
        $products = $dao->getTable('Vps_Dao_ProductProducts')->find($this->_productId);
        //fixme: raise error?
        $this->_product = $products->current();
        $this->_content = $this->createComponent('', $this->_product->component_id);
    }

    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);

        $ret['name'] = $this->_product->name;
        $ret['filename'] = $this->_product->filename;
        $ret['price'] = $this->_product->price;
        $ret['vat'] = $this->_product->vat;

        $ret['content'] = $this->_content->getTemplateVars($mode);

        if ($mode == 'edit') {
            $ret['template'] = dirname(__FILE__).'/Details.html';
        } else {
             $ret['template'] = 'Product/Details.html';
        }

        return $ret;
    }

    public function getComponentInfo()
    {
      return parent::getComponentInfo() + $this->_content->getComponentInfo();
    }

    public function saveFrontendEditing(Zend_Controller_Request_Http $request)
    {
        $this->_product->name = $request->getPost('name');
        $this->_product->price = $request->getPost('price');
        $this->_product->vat = $request->getPost('vat');
        $this->_product->save();

        $ret = parent::saveFrontendEditing($request);
        $ret['createComponents'] = $this->_content->getComponentInfo();
        return $ret;
    }
}
