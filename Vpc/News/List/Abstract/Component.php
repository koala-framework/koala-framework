<?php
abstract class Vpc_News_List_Abstract_Component extends Vpc_Abstract implements Vpc_Paging_ParentInterface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['paging'] = 'Vpc_News_List_Abstract_Paging_Component';
        $ret['childComponentClasses']['preview'] = 'Vpc_News_Detail_Preview_Component';
        return $ret;
    }

    abstract public function getNewsComponent();


    protected function _selectNews()
    {
        $select = $this->getTreeCacheRow()->getTable()->select();
        $select->setIntegrityCheck(false);
        $select->from($this->getTreeCacheRow()->getTable());

        $newsComponent = $this->getNewsComponent();
        $cClasses = Vpc_Abstract::getSetting($newsComponent->component_class, 'childComponentClasses');

        $select->where('vps_tree_cache.parent_component_id = ?', $newsComponent->component_id)
            ->where('vps_tree_cache.component_class = ?', $cClasses['preview'])
            ->join('vpc_news', 'vps_tree_cache.tag=vpc_news.id', array())
            ->where('publish_date <= NOW()')
            ->where('expiry_date >= NOW()');
        if (!$this->_showInvisible()) {
            $select->where('vps_tree_cache.visible = 1');
        }
        return $select;
    }

    public function getNews($limit = 15, $start = null)
    {
        $select = $this->_selectNews();
        $select->limit($limit, $start);
        $select->order('publish_date DESC');
        return $this->getTreeCacheRow()->getTable()->fetchAll($select);
    }

    public function getNewsRow($news_id)
    {
        $model = new Vpc_News_Directory_Model();
        return $model->find($news_id)->current();
    }

    public function getPagingCount()
    {
        $select = $this->_selectNews();
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->from(null, array('count' => 'COUNT(*)'));
        $r = $select->query()->fetchAll();
        return $r[0]['count'];
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paging'] = $this->getComponentId().'-paging';

        $limit = $this->getTreeCacheRow()->getTable()
            ->find($ret['paging'])
            ->current()->getComponent()->getLimit();

        $ret['news'] = $this->getNews($limit['limit'], $limit['start']);

        return $ret;
    }
}
