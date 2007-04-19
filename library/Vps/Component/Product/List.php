<?php
class Vps_Component_Product_List extends Vps_Component_Abstract
{
    private $_products;
    private $_categoryId;
    public function setCategoryId($id)
    {
        $this->_categoryId = $id;
    }

    private function getProducts()
    {
        if (!isset($this->_products)) {
            $dao = $this->getDao();
            $categories = $dao->getTable('Vps_Dao_ProductCategories')->find($this->_categoryId);
            //todo: raise error?
            $category = $categories->current();
            $this->_products = $category->findManyToManyRowset('Vps_Dao_ProductProducts', 'Vps_Dao_ProductProductsToCategories');
            //todo: visible berücksichtigen
        }
        return $this->_products;
    }

    protected function generateTreeHierarchy(Vps_PageCollection_Tree $pageCollection, $filename)
    {
        foreach($this->getProducts() as $row) {
            if ($filename != '' && $filename != $row->filename) continue;

            $component = $this->createPageInTree($pageCollection, 'Vps_Component_Product_Details', $row->filename, $this->getComponentId(), $row->id);
            if($component) $component->setProductId($row->id);
        }
    }
    
    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);

        $componentKey = $this->getComponentKey();
        if($componentKey!='') $componentKey .= ".";

        foreach($this->getProducts() as $row) {
            $teaser = new Vps_Component_Product_Teaser($this->_dao, $this->getComponentId(), '', $componentKey.$row->id);;
            $product = array('name'=>$row->name, 'filename'=>$row->filename,
                             'price'=>$row->price, 'vat'=>$row->vat);
            $teaser->setProductData($product);
            $this->_productTeasers[] = $teaser;
            $ret['products'][] = $teaser->getTemplateVars($mode);
        }
       	$ret['template'] = 'Product/List.html';
        return $ret;
    }
    public function getComponentInfo()
    {
        $ret = parent::getComponentInfo();
        foreach($this->_productTeasers as $component) {
            $ret += $component->getComponentInfo();
        }
        return $ret;
    }
}
