<?php
class Vpc_News_List_Feed_Component extends Vpc_Abstract_Feed_Component
{

    public function getNewsComponent()
    {
        $returnComponent = $this;
        while (!$returnComponent instanceof Vpc_News_Component) {
            $returnComponent = $returnComponent->getParentComponent();
        }
        return $returnComponent;
    }
    protected function _getRssEntries()
    {
        $ret = array();
        foreach ($this->getNewsComponent()->getNews(15) as $n) {
            $url = $this->getNewsComponent()->getPageFactory()
                    ->getChildPageByNewsRow($n)
                    ->getUrl();
            $ret[] = array(
                'title' => $n->title,
                'link' => 'http://'.$_SERVER['HTTP_HOST'].$url,
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
