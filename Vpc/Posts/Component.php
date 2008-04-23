<?php
class Vpc_Posts_Component extends Vpc_Abstract
{
    private $_posts;
    private $_paging;
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'     => 'Posts',
            'componentIcon'     => new Vps_Asset('comments'),
            'tablename'         => 'Vpc_Posts_Model',
            'hideInNews'        => true,
            'childComponentClasses' => array(
                'write' => 'Vpc_Posts_Write_Component',
                'post' =>  'Vpc_Posts_Post_Component',
                'paging' =>  'Vpc_Posts_Paging_Component',
            ),
            'loginDecorator' => 'Vpc_Decorator_CheckLogin_Component'
        ));
        return $ret;
    }

    protected function _getPagingComponent()
    {
        if (!isset($this->_paging)) {
            $classes = $this->_getSetting('childComponentClasses');
            $this->_paging = $this->createComponent($classes['paging'], 'paging');
            $select = $this->getTable()->getAdapter()->select();
            $select->from('vpc_posts', array('count'=>'COUNT(*)'))
                ->where('component_id=?', $this->getDbId())
                ->where('visible=1');
            $r = $select->query()->fetchAll();
            $this->_paging->setEntries($r[0]['count']);
        }
        return $this->_paging;
    }
    
    public function getPosts()
    {
        if (!isset($this->_posts)) {
            $this->_posts = array();
            $where = array(
                'component_id = ?' => $this->getDbId(),
                'visible = 1'
            );
            $classes = $this->_getSetting('childComponentClasses');
            $limit = $this->_getPagingComponent()->getLimit();
            $rows = $this->getTable()->fetchAll($where, 'id ASC',
                                            $limit['limit'], $limit['start']);
            foreach ($rows as $row) {
                $c = $this->createComponent($classes['post'], $row->id);
                $this->_posts[] = $c;
                $c->setPostNum(count($this->_posts));
            }
        }
        return $this->_posts;
    }
    public function getLastPosts()
    {
        //TODO: mit treecache wird alles besser :D
        $ret = array();
        $where = array(
            'component_id = ?' => $this->getDbId(),
            'visible = 1'
        );
        $classes = $this->_getSetting('childComponentClasses');
        $rows = $this->getTable()->fetchAll($where, 'id DESC', '5');
        $numPosts = $this->_getPagingComponent()->getEntries();
        foreach ($rows as $row) {
            $c = $this->createComponent($classes['post'], $row->id);
            $c->setPostNum($numPosts - count($ret));
            $ret[] = $c;
        }
        return $ret;
    }

    public function getChildComponents()
    {
        $ret = $this->getPosts();
        $ret[] = $this->_getPagingComponent();
        return $ret;
    }

    public function getChildComponentByRow($row)
    {
        $classes = $this->_getSetting('childComponentClasses');
        return $this->createComponent($classes['post'], $row->id);
    }

    public function getTemplateVars()
    {
        if (!(Zend_Registry::get('userModel')->getAllCache())) {
            Zend_Registry::get('userModel')->createAllCache();
        }

        $ret = parent::getTemplateVars();
        $ret['posts'] = array();
        foreach ($this->getPosts() as $c) {
            $ret['posts'][] = $c->getTemplateVars();
        }

        $ret['writeUrl'] = $this->getPageFactory()->getChildPageById('write')->getUrl();

        $ret['paging'] = $this->_getPagingComponent()->getTemplateVars();
        return $ret;
    }

}
