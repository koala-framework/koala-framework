<?php
abstract class Vpc_News_List_Abstract_Component extends Vpc_Abstract_Composite_Component
            implements Vpc_Paging_ParentInterface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['paging'] = 'Vpc_News_List_Abstract_Paging_Component';
        $ret['childComponentClasses']['view'] = 'Vpc_News_List_Abstract_View_Component';
        return $ret;
    }

    abstract public function getNewsComponent();

    protected function _getNewsTable()
    {
        return $this->getNewsComponent()->getComponent()->getTable();
    }


    protected function _selectNews()
    {
        $newsComponent = $this->getNewsComponent();
        if (!$newsComponent) return null;
        $select = $this->_getNewsTable()->select()
            ->from($this->_getNewsTable())
            ->where('component_id = ?', $newsComponent->db_id)
            ->where('publish_date <= NOW()');
        if (Vpc_Abstract::getSetting($this->getNewsComponent()->component_class, 'enableExpireDate')) {
            $select->where('expiry_date >= NOW()');
        }
        if (!$this->_showInvisible()) {
            $select->where('visible = 1');
        }
        return $select;
    }

    public function getNews($limit = null, $start = null)
    {
        $select = $this->_selectNews();
        if (!$select) return array();
        if (!$limit && !$start) {
            $l = $this->getTreeCacheRow()->findChildComponent('-paging')
                ->current()->getComponent()->getLimit();
            $limit = $l['limit'];
            $start = $l['start'];
        }
        $select->limit($limit, $start);
        $select->order('publish_date DESC');
        return $this->_getNewsTable()->fetchAll($select);
    }

    public function getNewsRow($news_id)
    {
        return $this->_getNewsTable()->find($news_id)->current();
    }

    public function getPagingCount()
    {
        $select = $this->_selectNews();
        if (!$select) return 0;
        $select->setIntegrityCheck(false);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->from(null, array('count' => 'COUNT(*)'));
        $r = $select->query()->fetchAll();
        return $r[0]['count'];
    }
}
