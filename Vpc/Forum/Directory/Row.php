<?php
class Vpc_Forum_Directory_Row extends Vpc_Row
{
    public function __toString()
    {
        return $this->name;
    }
    
    public function getLastPostId()
    {
        $componentIdPattern = $this->component_id . '_' . $this->id . '_%';
        $where = array(
            "component_id LIKE '$componentIdPattern'" => '',
            "visible = ?" => 1,
        );
        $model = new Vpc_Posts_Directory_Model();
        $last = $model->fetchAll($where, 'create_time DESC')->current();
        if ($last) {
            return $last->component_id . '_' . $last->id;
        }
        return null;
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
        $componentIdPattern = $this->component_id . '_' . $this->id . '_%';
        $where = array(
            "component_id LIKE '$componentIdPattern'" => '',
            'visible = ?' => 1
        );
        $model = new Vpc_Posts_Directory_Model();
        return count($model->fetchAll($where));
    }

}
