<?php
class Vpc_News_Category_Detail_Component extends Vpc_News_List_Abstract_Component
{
    protected function _selectNews()
    {
        $select = parent::_selectNews();
        $select->join('vpc_news_to_categories',
                      'vpc_news_to_categories.news_id=vpc_news.id',
                      array());
        $categoryId = $this->getTreeCacheRow()->tag;
        $select->where('vpc_news_to_categories.category_id = ?', $categoryId);
        return $select;
    }

    public function getNewsComponent()
    {
        return $this->getTreeCacheRow()
            ->findParentComponent() //categories directory
            ->getNewsComponent();
    }
}
