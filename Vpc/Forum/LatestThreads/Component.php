<?php
class Vpc_Forum_LatestThreads_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Forum.Latest Threads');
        $ret['childModel'] = 'Vpc_Forum_Group_Model';
        $ret['numberOfThreads'] = 5;
        $ret['forumClass'] = 'Vpc_Forum_Directory_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['threads'] = array();
        $forum = Vps_Component_Data_Root::getInstance()->getComponentByClass(
            $this->_getSetting('forumClass'),
            array('subroot' => $this->getData())
        );
        $groupComponentClass = Vpc_Abstract::getChildComponentClass($forum->componentClass, 'groups');
        $threadComponentClass = Vpc_Abstract::getChildComponentClass($groupComponentClass, 'detail');
        $postsComponentClass = Vpc_Abstract::getChildComponentClass($threadComponentClass, 'child', 'posts');

        $groupGenerator = Vps_Component_Generator_Abstract::getInstance($forum->componentClass, 'groups');
        $threadGenerator = Vps_Component_Generator_Abstract::getInstance($groupComponentClass, 'detail');
        $postGenerator = Vps_Component_Generator_Abstract::getInstance($postsComponentClass, 'detail');

        $select = $postGenerator->select(null);
        $select = $postGenerator->joinWithParentGenerator($select, $threadGenerator);
        $select = $threadGenerator->joinWithParentGenerator($select, $groupGenerator, $forum);
        $select->limit($this->_getSetting('numberOfThreads'));
        $select->order('create_time', 'DESC');
        $threads = array();
        $threadIds = array();

        $x = 0;
        while (count($threads) < $this->_getSetting('numberOfThreads') && $x < 5) {
            $x++;
            $posts = $postGenerator->getChildData(null, $select);
            foreach ($posts as $post) {
                $thread = $post->parent->parent;
                if (in_array($thread->componentId, $threadIds)) continue;
                foreach ($thread->getComponent()->getThreadVars() as $key => $val) {
                    $thread->$key = $val;
                }
                $threadIds[] = $thread->componentId;
                $threads[] = $thread;
            }
            if (isset($post) && $post) {
                $select->where('vpc_posts.create_time < ?', $post->row->create_time);
            } else {
                break;
            }
        }
        $ret['threads'] = $threads;
        return $ret;
    }

    public static function getStaticCacheVars($componentClass)
    {
        $ret = array();
        $ret[] = array(
            'model' => 'Vpc_Posts_Directory_Model'
        );
        $ret[] = array(
            'model' => Vps_Registry::get('config')->user->model
        );
        return $ret;
    }
}
