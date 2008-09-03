<?php
class Vpc_Forum_Directory_Row extends Vpc_Row
{
    public function __toString()
    {
        return $this->name;
    }
    
    public function getLastPostId()
    {
        $db = Zend_Registry::get('db');
        $componentIdPattern = $this->component_id . '_' . $this->id . '_%';
        $sql = "SELECT CONCAT(p.component_id, '-', p.id) component_id
            FROM vpc_posts p, vpc_forum_threads t
            WHERE p.component_id=CONCAT(t.component_id, '_', t.id, '-posts')
                AND p.component_id LIKE '$componentIdPattern'
            ORDER BY p.create_time DESC
            LIMIT 1
        ";
        return $db->fetchOne($sql);
    }
    
    public function countThreads()
    {
        $where = array(
            'component_id = ?' => $this->component_id . '_' . $this->id
        );
        $model = new Vpc_Forum_Group_Model();
        return count($model->fetchAll($where));
    }
    
    public function countPosts()
    {
        $db = Zend_Registry::get('db');
        $componentIdPattern = $this->component_id . '_' . $this->id . '_%';
        $sql = "SELECT COUNT(*)
            FROM vpc_posts p, vpc_forum_threads t
            WHERE p.component_id=CONCAT(t.component_id, '_', t.id, '-posts')
                AND p.component_id LIKE '$componentIdPattern'
        ";
        return $db->fetchOne($sql);
    }
}
