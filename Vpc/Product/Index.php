<?php
class Vpc_Product_Index extends Vpc_Abstract
{
    private $_names;
    
    protected function getChildPages($filename = '')
    {
        $dao = $this->getDao();
        $where = $dao->getDb()->quoteInto('visible = ?', '1');
        $rows = $dao->getTable('Vps_Dao_ProductCategories')->fetchAll($where);

        $components = array();
        foreach($rows as $row) {
            if ($filename != '' && $filename != $row->filename) continue;

            $component = $this->createComponent('Vpc_Product_List', 0, $row->id);
            $component->setCategoryId($row->id);
            $components[$row->filename] = $component;
            $this->_names[$row->filename] = $row->name;
        }
        return $components;
    }
    
    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);
        $pages = $this->generateHierarchy();
        foreach($pages as $filename => $page) {
            $data['name'] = $this->_names[$filename];
            $data['filename'] = $page->getPath();
            $ret['categories'][] = $data;
        }

        $ret['template'] = 'Product/Categories.html';
        return $ret;
    }

    public function getChildComponents()
    {
        return $this->getChildPages();
    }
}
