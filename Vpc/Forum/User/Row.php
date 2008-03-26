<?php
class Vpc_Forum_User_Row extends Vps_Db_Table_Row_Abstract
{
    protected $_cacheImages = array(
        'avatarmini' => array(0, 50),
        'avatar' => array(150, 150)
    );

    public function __toString()
    {
        return trim($this->nickname.' '.Zend_Registry::get('userModel')->find($this->id)->current()->firstname);
    }

    public function getNumPosts()
    {
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
        $threads = new Vpc_Forum_Thread_Model();

        $info = $threads->info();
        $select = new Zend_Db_Select($threads->getAdapter());
        $select->from($info['name'], 'COUNT(*)');
        $select->where('user_id = ?', $this->id);
        return $select->query()->fetchColumn();
    }
}