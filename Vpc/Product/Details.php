<?p
class Vpc_Product_Details extends Vpc_Abstra

    private $_productI
    private $_produc
    private $_conten
  
    public function setProductId($i
   
        $this->_productId = $i
   
  
    protected function setup
   
        $dao = $this->getDao(
        if (!isset($this->_productId))
            $this->_productId = substr($this->getPageKey(), strpos($this->getPageKey(), '.') + 1
       
        $products = $dao->getTable('Vps_Dao_ProductProducts')->find($this->_productId
        //fixme: raise erro
        $this->_product = $products->current(
        $this->_content = $this->createComponent('', $this->_product->component_id
   

    public function getTemplateVars
   
        $ret = parent::getTemplateVars(

        $ret['name'] = $this->_product->nam
        $ret['filename'] = $this->_product->filenam
        $ret['price'] = $this->_product->pric
        $ret['vat'] = $this->_product->va
        $ret['content'] = $this->_content->getTemplateVars(
        $ret['template'] = 'Product/Details.html

        return $re
   

    public function getComponentInfo
   
      return parent::getComponentInfo() + $this->_content->getComponentInfo(
   

    public function saveFrontendEditing(Zend_Controller_Request_Http $reques
   
        $this->_product->name = $request->getPost('name'
        $this->_product->price = $request->getPost('price'
        $this->_product->vat = $request->getPost('vat'
        $this->_product->save(

        $ret = parent::saveFrontendEditing($request
        $ret['createComponents'] = $this->_content->getComponentInfo(
        return $re
   
  
    public function getChildComponents
   
        $this->setup(
        return array($this->_content
   

