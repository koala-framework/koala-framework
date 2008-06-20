<?php
class Vpc_News_Category_ShowCategories_Component extends Vpc_News_List_Abstract_Component
{
    private $_newsTreeCacheRow;
    private $_categories;
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('News.show Categories');
        $ret['showNewsClass'] = 'Vpc_News_Directory_Component';
        return $ret;
    }

    private function _getCategories()
    {
        if (!isset($this->_categories)) {
            $t = new Vpc_News_Category_ShowCategories_Model();
            $this->_categories = $t->fetchAll(array('component_id = ?' => $this->getDbId()));
        }
        return $this->_categories;
    }

    protected function _getNewsComponent()
    {
        if (!isset($this->_newsTreeCacheRow)) {
            $categories = $this->_getCategories();
            if (count($categories)) {
                $this->_newsTreeCacheRow = $this->getTreeCacheRow()->getTable()
                    ->findByDbId($categories->current()->news_component_id)->current();
            } else {
                $this->_newsTreeCacheRow = null;
            }
        }
        return $this->_newsTreeCacheRow;
    }

    protected function _selectNews()
    {
        $select = parent::_selectNews();
        if (!$select) return null;

        $select->join('vpc_news_to_categories',
                      'vpc_news_to_categories.news_id=vpc_news.id',
                      array());
        $ids = array();
        $db = $this->getTreeCacheRow()->getTable()->getAdapter();
        foreach ($this->_getCategories() as $category) {
            $ids[] = $db->quote($category->category_id);
        }
        if (!$ids) return null;
        $select->where('vpc_news_to_categories.category_id IN ('.implode(',', $ids).')');
        return $select;
    }
}
