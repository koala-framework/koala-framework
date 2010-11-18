<?php
class Vpc_Forum_Group_Model extends Vps_Model_Db
{
    protected $_table = 'vpc_forum_threads';
    protected $_rowClass = 'Vpc_Forum_Group_Row';

    protected $_referenceMap = array(
        'User' => array(
            'column' => 'user_id',
            'refModelClass' => null
        )
    );

    protected function _init()
    {
        $userModelClass = get_class(Vps_Registry::get('userModel'));
        $this->_referenceMap['User']['refModelClass'] = $userModelClass;

        parent::_init();
    }

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_AutoFill('{component_id}_{id}-posts');
        $this->_filters = array('cache_child_component_id' => $filter);
    }

    public function fetchAll($where = null, $order = null, $limit = null, $start = null)
    {
        if ($order == null) {
            $order = "(SELECT vpc_posts.id FROM vpc_posts
                    WHERE vpc_posts.component_id=CONCAT(vpc_forum_threads.component_id,
                    '_', vpc_forum_threads.id, '-posts') ORDER BY id DESC LIMIT 1) DESC";
            $order = new Zend_Db_Expr($order);
        }
        return parent::fetchAll($where, $order, $limit, $start);
    }

    public function getNumThreads($dbId)
    {
        return $this->countRows($this->select()
            ->whereEquals('component_id', $dbId)
        );
    }
    public function getNumPosts($dbId)
    {
        $info = $this->info();
        $t = $info['name'];
        $select = new Zend_Db_Select($this->getAdapter());
        $select->from($t, 'COUNT(*)');
        $select->where("$t.component_id = ?", $dbId);
        $select->join('vpc_posts', "vpc_posts.component_id=
            CONCAT($t.component_id, '_', $t.id, '-posts')",
            array());
        return $select->query()->fetchColumn();
    }
}