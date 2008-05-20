<?php
class Vpc_News_List_Abstract_Feed_Component extends Vpc_Abstract_Feed_Component
{

    public function getNewsComponent()
    {
        return $this->getTreeCacheRow()->findParentComponent()->getComponent();
    }
    protected function _getRssEntries()
    {
        $ret = array();
        foreach ($this->getNewsComponent()->getNews(15) as $n) {
            $ret[] = array(
                'title' => $n->name,
                'link' => 'http://'.$_SERVER['HTTP_HOST'].$n->url,
                'description' => $n->teaser,
                'lastUpdate' => strtotime($n->publish_date)
            );
        }
        return $ret;
    }

    protected function _getRssTitle()
    {
        return parent::_getRssTitle().' - '.trlVps('News Feed');
    }
}
