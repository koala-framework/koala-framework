<?php

abstract class Vpc_News_List_Abstract_Component extends Vpc_Abstract
{

    public function getNews()
    {
        return array();
    }

    public function getNewsComponent()
    {
        $returnComponent = $this;
        while (!$returnComponent instanceof Vpc_News_Component) {
            $returnComponent = $returnComponent->getParentComponent();
        }
        return $returnComponent;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['news'] = array();

        if ($this->getNewsComponent()) {
            foreach ($this->getNews() as $row) {
                $n = $this->getNewsComponent()->getPageFactory()->getChildPageByNewsRow($row);

                $data = $row->toArray();
                $data['href'] = $n->getUrl();
                $ret['news'][] = $data;
            }
        }
        return $ret;
    }

}