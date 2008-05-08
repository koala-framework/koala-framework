<?php
class Vpc_News_Titles_Component extends Vpc_News_List_Abstract_Component implements Vpc_News_Titles_Interface_Component
{

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'News.Titles',
            'componentIcon' => new Vps_Asset('newspaper'),
            'tablename'     => 'Vpc_News_Titles_Model'
        ));
    }

    public function getNewsComponent()
    {
        $row = $this->_getRow();
        if ($row && $row->news_component_id) {
            $pc = $this->getPageCollection();
            return $pc->getComponentById($row->news_component_id);
        } else {
            return null;
        }
    }

    public function getNews($limit = 15, $start = null)
    {
        return $this->getNewsComponent()->getNews(5);
    }

    public function getNewsCount()
    {
        //todo: langsam und unschön
        return count($this->getNews(null, null));
    }

    public function getTemplateVars()
    {
        return parent::getTemplateVars();
    }

}
