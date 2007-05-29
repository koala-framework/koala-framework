<?php
class Vpc_Product_List extends Vpc_Abstract
{
    private $_products;
    private $_categoryId;
    private $_productTeasers = array();
    
    public function setCategoryId($id)
    {
        $this->_categoryId = $id;
    }

    protected function createComponents($filename = '')
    {
        $dao = $this->getDao();
        $categories = $dao->getTable('Vps_Dao_ProductCategories')->find($this->_categoryId);
        //todo: raise error?
        $category = $categories->current();
        $rows = $category->findManyToManyRowset('Vps_Dao_ProductProducts', 'Vps_Dao_ProductProductsToCategories');
        //todo: visible ber�cksichtigen

        $components = array();
        foreach($rows as $row) {
            if ($filename != '' && $filename != $row->filename) continue;

            $component = $this->createComponent('Vpc_Product_Details', 0, $row->id);
            $component->setProductId($row->id);
            $components[$row->filename] = $component;
            $this->_products[$row->filename] = $row;
        }
        return $components;
    }
    
    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);

        $pages = $this->generateHierarchy();
        foreach ($pages as $filename => $page) {
            $row = $this->_products[$filename];
            
            $teaser = $this->createComponent('Vpc_Product_Teaser', 0, '', $row->id);
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
    
    public function getChildComponents()
    {
        return $this->createComponents();
    }
}
