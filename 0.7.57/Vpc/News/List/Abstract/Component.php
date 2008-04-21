<?php

abstract class Vpc_News_List_Abstract_Component extends Vpc_Abstract
{
    private $_paging;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['paging'] = 'Vpc_News_List_Paging_Component';
        return $ret;
    }

    abstract public function getNews($limit = 15, $start = null);
    abstract public function getNewsCount();

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
            $limit = $this->_getPagingComponent()->getLimit();
            foreach ($this->getNews($limit['limit'], $limit['start']) as $row) {
                $n = $this->getNewsComponent()->getPageFactory()->getChildPageByNewsRow($row);

                $data = $row->toArray();
                $data['href'] = $n->getUrl();
                $ret['news'][] = $data;
            }
        }

        $ret['paging'] = $this->_getPagingComponent()->getTemplateVars();
        return $ret;
    }

    protected function _getPagingComponent()
    {
        if (!isset($this->_paging)) {
            $classes = $this->_getSetting('childComponentClasses');
            $this->_paging = $this->createComponent($classes['paging'], 'paging');
            $this->_paging->setEntries($this->getNewsCount());
        }
        return $this->_paging;
    }

    public function getChildComponents()
    {
        return array($this->_getPagingComponent());
    }

}