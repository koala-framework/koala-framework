<?php
class Vpc_Forum_User_View_Guestbook_Component extends Vpc_Forum_Posts_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['post'] = 'Vpc_Forum_User_View_Guestbook_Post_Component';
        $ret['childComponentClasses']['write'] = 'Vpc_Forum_User_View_Guestbook_Write_Component';
        return $ret;
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

            $tableInfo = $this->getTable()->info();
            $select = Zend_Registry::get('db')->select()
                ->from($tableInfo['name'], array('count'=>'COUNT(*)'));
            foreach ($where as $k => $v) {
                if (is_string($k)) {
                    $select->where($k, $v);
                } else {
                    $select->where($v);
                }
            }
            $postCount = $select->query()->fetchAll();
            $postCount = $postCount[0]['count'] - $limit['start'];

            $rows = $this->getTable()->fetchAll($where, 'id DESC',
                                            $limit['limit'], $limit['start']);
            foreach ($rows as $row) {
                $c = $this->createComponent($classes['post'], $row->id);
                $this->_posts[] = $c;
                $c->setPostNum($postCount--);
            }
        }
        return $this->_posts;
    }

    public function getTemplateVars()
    {
        $ret = Vpc_Posts_Component::getTemplateVars();
        return $ret;
    }

    public function getGroupComponent()
    {
        return null;
    }

    public function getForumComponent()
    {
        return $this->getParentComponent()->getParentComponent()->getParentComponent();
    }
}