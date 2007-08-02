<?php
/**
 * @package Vpc
 * @subpackage Components
 */
class Vpc_Product_Index extends Vpc_Abstract
{
    private $_names;
    
    protected function getChildPages($filename = '')
    {
        $dao = $this->getDao();
        $where = $dao->getDb()->quoteInto('visible = ?', '1');
        $rows = $dao->getTable('Vps_Dao_ProductCategories')->fetchAll($where);

        $pages = array();
        foreach($rows as $row) {
            if ($filename != '' && $filename != $row->filename) continue;

            $page = $this->createPage('Vpc_Product_List', 0, $row->id);
            $page->setCategoryId($row->id);
            $pages[$row->filename] = $page;
            $this->_names[$row->filename] = $row->name;
        }
        return $pages;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $pages = $this->generateHierarchy();
        foreach($pages as $filename => $page) {
            $data['name'] = $this->_names[$filename];
            $data['filename'] = $page->getUrl();
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
