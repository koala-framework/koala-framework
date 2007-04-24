<?php
class Vps_Component_Product_Categories extends Vps_Component_Abstract
{
    private $_categories;
    private function getCategories()
    {
        if (!isset($this->_categories)) {
            $dao = $this->getDao();
            $where = $dao->getDb()->quoteInto('visible = ?', '1');
            $this->_categories = $dao->getTable('Vps_Dao_ProductCategories')->fetchAll($where);
        }
        return $this->_categories;
    }

    protected function createComponents($filename)
    {
        $components = array();
        foreach($this->getCategories() as $row) {
            if ($filename != '' && $filename != $row->filename) continue;

            $component = $this->createComponent('Vps_Component_Product_List', 0, $row->id);
            $component->setCategoryId($row->id);
            $components[$row->filename] = $component;
        }
        return $components;
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
