<?php
class Vpc_Forum_User_Row extends Vps_Db_Table_Row_Abstract
{
    public function __toString()
    {
        return Zend_Registry::get('userModel')->find($this->id)->current()->__toString();
    }

    public function getNumPosts()
    {
        // Model irgendwie über komponente holen?
        $posts = new Vpc_Posts_Model();

        $info = $posts->info();
        $select = new Zend_Db_Select($posts->getAdapter());
        $select->from($info['name'], 'COUNT(*)');
        $select->where('user_id = ?', $this->id);
        $select->where('visible = 1');
        return $select->query()->fetchColumn();
    }

    public function getNumThreads()
    {
        // Model irgendwie über komponente holen?
        $threads = new Vpc_Forum_Thread_Model();

        $info = $threads->info();
        $select = new Zend_Db_Select($threads->getAdapter());
        $select->from($info['name'], 'COUNT(*)');
        $select->where('user_id = ?', $this->id);
        return $select->query()->fetchColumn();
    }
}