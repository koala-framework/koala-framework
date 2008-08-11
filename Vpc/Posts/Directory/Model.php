<?php
class Vpc_Posts_Directory_Model extends Vpc_Table
{
    protected $_name = 'vpc_posts';
    protected $_rowClass = 'Vpc_Posts_Directory_Row';

    protected function _setup()
    {
        $this->_referenceMap['User'] = array(
            'columns'           => array('user_id'),
            'refTableClass'     => get_class(Vps_Registry::get('userModel')),
            'refColumns'        => array('id')
        );
        parent::_setup();
    }

    public function getLastPost($dbId)
    {
        $where = array();
        $where['component_id = ?'] = $dbId;
        $where[] = 'visible = 1';
        return $this->fetchAll($where, 'id DESC', 1)->current();
    }
    
    public function getNumPosts($dbId)
    {
        $info = $this->info();
        $select = new Zend_Db_Select($this->getAdapter());
        $select->from($info['name'], 'COUNT(*)');
        $select->where('component_id = ?', $dbId);
        $select->where('visible = 1');
        return $select->query()->fetchColumn();
    }

    public function getNumReplies($dbId)
    {
        return $this->getNumPosts($dbId)-1;
    }
}
