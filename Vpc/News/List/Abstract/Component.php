<?php
abstract class Vpc_News_List_Abstract_Component extends Vpc_Abstract_Composite_Component
            implements Vpc_Paging_ParentInterface
{
    private $_newsComponent = false;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['paging'] = 'Vpc_News_List_Abstract_Paging_Component';
        $ret['childComponentClasses']['view'] = 'Vpc_News_List_Abstract_View_Component';
        return $ret;
    }

    public function getNewsComponent()
    {
        if ($this->_newsComponent === false) {
            $this->_newsComponent = $this->_getNewsComponent();
        }
        return $this->_newsComponent;
    }
    abstract protected function _getNewsComponent();

    protected function _selectNews()
    {
        if (!$this->getNewsComponent()) return null;
        $select = $this->getNewsComponent()->getTreeCache('Vpc_News_Directory_TreeCacheDetail')
            ->select($this->getNewsComponent());
        $select->where('publish_date <= NOW()');
        if (Vpc_Abstract::getSetting($this->getNewsComponent()->componentClass, 'enableExpireDate')) {
            $select->where('expiry_date >= NOW()');
        }
        return $select;
    }

    public function getNews($limit = null, $start = null)
    {
        $select = $this->_selectNews();
        if (!$select) return array();
        if (!$limit && !$start) {
            $l = $this->getData()->getChildComponent('-paging')
                ->getComponent()->getLimit();
            $limit = $l['limit'];
            $start = $l['start'];
        }
        $select->limit($limit, $start);
        $select->order('publish_date DESC');
        $select->group('vpc_news.id');
        $constraints = array(
            'treecache' => 'Vpc_News_Directory_TreeCacheDetail',
            'select' => $select
        );
        return $this->getNewsComponent()->getChildComponents($constraints);
    }

    public function getPagingCount()
    {
        $select = $this->_selectNews();
        if (!$select) return 0;
        $select->setIntegrityCheck(false);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->from(null, array('count' => 'COUNT(DISTINCT vpc_news.id)'));
        $r = $select->query()->fetchAll();
        return $r[0]['count'];
    }
}
