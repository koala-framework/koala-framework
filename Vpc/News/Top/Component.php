<?php
class Vpc_News_Top_Component extends Vpc_News_List_Abstract_Component
{
    private $_newsData;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'News.Top',
            'componentIcon' => new Vps_Asset('newspaper'),
            'tablename'     => 'Vpc_News_Top_Model',
            'default'       => array(),
            'limit'         => 5
        ));
    }

    public function getNewsComponent()
    {
        if (!isset($this->_newsData)) {
            $row = $this->_getRow();
            if ($row && $row->news_component_id) {
                $this->_newsData = Vps_Component_Data_Root::getInstance()->getByDbId($row->news_component_id);
            } else {
                $this->_newsData = null;
            }
        }
        return $this->_newsData;
    }

    public function getNews($limit = 15, $start = null)
    {
        $c = $this->getNewsComponent();
        if (!$c) return array();
        return $c->getComponent()->getNews($this->_getSetting('limit'));
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
