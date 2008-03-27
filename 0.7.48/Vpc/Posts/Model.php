<?php
class Vpc_Posts_Model extends Vps_Db_Table_Abstract
{
    protected $_name = 'vpc_posts';
    protected $_rowClass = 'Vpc_Posts_Row';

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
