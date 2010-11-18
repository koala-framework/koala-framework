<?php
class Vpc_User_Detail_GeneralCommunity_Rating_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Rating';
        $ret['pointsPerPost'] = 1;
        $ret['pointsPerThread'] = 5;
        $ret['starsTable'] = array(
            //points => stars
            0 => 1,
            10 => 2,
            100 => 3,
            200 => 4,
            300 => 5
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['rating'] = $this->getRating();
        return $ret;
    }

    public function getRating()
    {
        $userId = $this->getData()->parent->parent->row->id;
        $select = Zend_Registry::get('db')->select();
        $select->from(array('p'=> 'vpc_posts'), array('count'=>'COUNT(p.id)'))
                ->where('p.user_id = ?', $userId);
        $posts = $select->query()->fetchAll();
        $posts = $posts[0]['count'];

        $select = Zend_Registry::get('db')->select();
        $select->from(array('t'=> 'vpc_forum_threads'), array('count'=>'COUNT(t.id)'))
                ->where('t.user_id = ?', $userId);
        $threads = $select->query()->fetchAll();
        $threads = $threads[0]['count'];

        $points = $posts*$this->_getSetting('pointsPerPost') + $threads*$this->_getSetting('pointsPerThread');
        $ret = 0;
        foreach ($this->_getSetting('starsTable') as $p=>$stars) {
            if ($points > $p) {
                $ret = $stars;
            }
        }
        return $ret;
    }

    public static function getStaticCacheVars()
    {
        $ret = array();
        $ret[] = array(
            'model' => 'Vpc_Forum_Directory_Model'
        );
        $ret[] = array(
            'model' => 'Vpc_Posts_Directory_Model'
        );
        return $ret;
    }
}
