<?p
class Vpc_Product_List extends Vpc_Abstra

    private $_product
    private $_categoryI
    private $_productTeasers = array(
  
    public function setCategoryId($i
   
        $this->_categoryId = $i
   

    protected function getChildPages($filename = '
   
        $dao = $this->getDao(
        $categories = $dao->getTable('Vps_Dao_ProductCategories')->find($this->_categoryId
        //todo: raise erro
        $category = $categories->current(
        $rows = $category->findManyToManyRowset('Vps_Dao_ProductProducts', 'Vps_Dao_ProductProductsToCategories'
        //todo: visible ber�cksichtig

        $pages = array(
        foreach($rows as $row)
            if ($filename != '' && $filename != $row->filename) continu

            $page = $this->createPage('Vpc_Product_Details', 0, $row->id
            $page->setProductId($row->id
            $pages[$row->filename] = $pag
            $this->_products[$row->filename] = $ro
       
        return $page
   
  
    public function getTemplateVars
   
        $ret = parent::getTemplateVars(

        $pages = $this->generateHierarchy(
        foreach ($pages as $filename => $page)
            $row = $this->_products[$filename
          
            $teaser = $this->createComponent('Vpc_Product_Teaser', 0, $row->id
            $product = array('name'=>$row->name, 'filename'=>$row->filename
                             'price'=>$row->price, 'vat'=>$row->vat)
            $teaser->setProductData($product
            $this->_productTeasers[] = $tease
            $ret['products'][] = $teaser->getTemplateVars(
       
        $ret['template'] = 'Product/List.html
      
        return $re
   
  
    public function getComponentInfo
   
        $ret = parent::getComponentInfo(
        foreach($this->_productTeasers as $component)
            $ret += $component->getComponentInfo(
       
        return $re
   
  
    public function getChildComponents
   
        return $this->getChildPages(
   

