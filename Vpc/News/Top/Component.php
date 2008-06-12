<?php
class Vpc_News_Top_Component extends Vpc_News_List_Abstract_Component
{
    private $_newsTreeCacheRow;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'News.Top',
            'componentIcon' => new Vps_Asset('newspaper'),
            'tablename'     => 'Vpc_News_Top_Model',
            'default'       => array()
        ));
    }

    public function getNewsComponent()
    {
        if (!isset($this->_newsTreeCacheRow)) {
            $row = $this->_getRow();
            if ($row && $row->news_component_id) {
                $this->_newsTreeCacheRow = $this->getTreeCacheRow()->getTable()
                    ->findByDbId($row->news_component_id)->current();
            } else {
                $this->_newsTreeCacheRow = null;
            }
        }
        return $this->_newsTreeCacheRow;
    }

    public function getNews($limit = 15, $start = null)
    {
        return $this->getNewsComponent()->getComponent()->getNews(5);
    }

    public function getPagingCount()
    {
        //todo: langsam und unschön
        return count($this->getNews(null, null));
    }

    public function getTemplateVars()
    {
        return parent::getTemplateVars();
    }

}
