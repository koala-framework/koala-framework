<?php
class Vpc_News_Category_Detail_Component extends Vpc_News_List_Abstract_Component
{
    protected function _selectNews()
    {
        $select = parent::_selectNews();
        $select->join('vpc_news_to_categories',
                      'vpc_news_to_categories.news_id=vpc_news.id',
                      array());
        $categoryId = $this->getData()->id;
        $select->where('vpc_news_to_categories.category_id = ?', $categoryId);
        $select->group('vps_news.id');
        return $select;
    }

    protected function _getNewsComponent()
    {
        return $this->getData()->parent->getComponent() //categories directory
            ->getNewsComponent();
    }
}
