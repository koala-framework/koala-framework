<?php
class Vpc_Forum_Search_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['paging'] = 'Vpc_Forum_Search_Paging_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['action'] = $this->getUrl();
        $ret['searchText'] = $this->_getParam('search');

        $ret['results'] = array();
        if (strlen($this->_getParam('search')) > 3) {
            $searchText = '%'.$this->_getParam('search').'%';

            $select = Zend_Registry::get('db')->select()
                ->from(array('t'=>'vpc_forum_threads'),
                       array('count'=>'COUNT(DISTINCT t.id)'))
                ->join(array('p'=>'vpc_posts'),
                        "p.component_id = CONCAT(t.component_id, '_', t.id, '-posts')",
                        array())
                ->where('t.subject LIKE ? OR p.content LIKE ?', $searchText)
                ->order('p.create_time DESC');
            $count = $select->query()->fetchAll();
            $this->getChildComponent('paging')->setEntries($count[0]['count']);

            $select->reset(Zend_Db_Select::COLUMNS);
            $select->from(null, array('t.id', 't.component_id', 't.subject'));
            $select->group('t.id');
            $limit = $this->getChildComponent('paging')->getLimit();
            $select->limit($limit['limit'], $limit['start']);

            $threads = $select->query()->fetchAll();
            foreach ($threads as $t) {
                preg_match('#_([0-9]+)$#', $t['component_id'], $m); //TODO: TreeCache?!
                $groupId = $m[1];
                $groupPage = $this->getParentComponent()->getPageFactory()
                                ->getChildPageById($groupId);
                $threadPage = $groupPage->getPageFactory()->getChildPageById($t['id']);
                $ret['results'][] = array_merge(
                    array('rel' => ''),
                    $threadPage->getThreadVars()
                );
            }
        }
        //TODO: suboptimal, muss nochmal ausgeführt werden obwohls in
        //Composite_Abstract schon gemacht wird (da ist aber Entries noch nicht gestetzt)
        //mögliche lösung: ein "processInput"-Schritt
        $ret['paging'] = $this->getChildComponent('paging')->getTemplateVars();
        return $ret;
    }
}
