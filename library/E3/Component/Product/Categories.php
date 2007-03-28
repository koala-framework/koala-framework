<?php
class E3_Component_Product_Categories extends E3_Component_Abstract
{
    private $_categories;
    private function getCategories()
    {
        if (!isset($this->_categories)) {
            $dao = $this->getDao();
            $where = $dao->getDb()->quoteInto('visible = ?', '1');
            $this->_categories = $dao->getTable('E3_Dao_ProductCategories')->fetchAll($where);
        }
        return $this->_categories;
    }

    protected function generateTreeHierarchy(E3_PageCollection_Tree $pageCollection, $filename)
    {
        foreach($this->getCategories() as $row) {
            if ($filename != '' && $filename != $row->filename) continue;

            $component = $this->createPageInTree($pageCollection, 'E3_Component_Product_List', $row->filename, $this->getComponentId(), $row->id);
            if($component) $component->setCategoryId($row->id);
        }
    }
    
    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);
        foreach($this->getCategories() as $row) {
            $new = array('name'=>$row->name, 'filename'=>$row->filename);
            $ret['categories'][] = $new;
        }
       	$ret['template'] = 'Product/Categories.html';
        return $ret;
    }
}
