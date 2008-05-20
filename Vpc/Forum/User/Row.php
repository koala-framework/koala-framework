<?php
class Vpc_Forum_User_Row extends Vps_Db_Table_Row_Abstract
{
    protected $_cacheImages = array(
        'avatarsmall' => array(40, 40, Vps_Media_Image::SCALE_CROP),
        'avatar' => array(150, 0)
    );

    public function __toString()
    {
        $ret = $this->nickname;
        if (!$ret) {
            $row = Zend_Registry::get('userModel')->find($this->id)->current();
            if ($row) {
                return $row->firstname;
            } else {
                return '';
            }
        }
        return $ret;
    }

    public function getNumPosts()
    {
        // todo: komponente holen un getTable() machen => auf treeComponentCache warten
        $posts = new Vpc_Posts_Model(array('componentClass' => ''));

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

    public function getRating()
    {
        $select = Zend_Registry::get('db')->select();
        /*
        $select->from(array('t'=> 'vpc_forum_threads'), array())
                ->joinRight(array('p'=> 'vpc_posts'),
                    "p.component_id=CONCAT(t.component_id, '_', t.id, '-posts')",
                    array('count'=>'COUNT(p.id)'))
                ->where('p.user_id = ?', $this->id);
        $posts = $select->query()->fetchAll();
        $posts = $posts[0]['count'];
        */
        $select = Zend_Registry::get('db')->select();
        $select->from(array('p'=> 'vpc_posts'), array('count'=>'COUNT(p.id)'))
                ->where('p.user_id = ?', $this->id);
        $posts = $select->query()->fetchAll();
        $posts = $posts[0]['count'];

        $select = Zend_Registry::get('db')->select();
        $select->from(array('t'=> 'vpc_forum_threads'), array('count'=>'COUNT(t.id)'))
                ->where('t.user_id = ?', $this->id);
        $threads = $select->query()->fetchAll();
        $threads = $threads[0]['count'];

        //todo einstellbar machen
        $points = $posts*1 + $threads*5;
        $starsTable = array(
            //points => stars
            0 => 1,
            10 => 2,
            100 => 3,
            200 => 4,
            300 => 5
        );
        $ret = 0;
        foreach ($starsTable as $p=>$stars) {
            if ($points > $p) {
                $ret = $stars;
            }
        }
        return $ret;
    }
}