<?php
class Vpc_Forum_Thread_Move_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $ret['threadVars'] = $this->getParentComponent()->getThreadVars();
        $ret['groups'] = '';
        $ret['groupsTemplate'] = '';
        $ret['threadMoved'] = false;
        $ret['groupUrl'] = '';

        if ($this->getGroupComponent()->mayModerate()) {
            if ($this->_getParam('to')) {
                $redirectUrl = $this->getGroupComponent()->getUrl();

                // thread verschieben
                $threadModel = new Vpc_Forum_Thread_Model();
                $threadRow = $threadModel->fetchRow(
                    array('id = ?' => $ret['threadVars']['thread_id'])
                );
                if ($threadRow) {
                    $threadComponentIdBefore = $threadRow->component_id;
                    $threadRow->component_id =
                        preg_replace('/[0-9]+$/', $this->_getParam('to'), $threadRow->component_id);
                    $threadRow->save();

                    // posts verschieben
                    $postsModel = new Vpc_Posts_Model(array('componentClass' => ''));
                    $where = array('component_id LIKE ?' => "{$threadComponentIdBefore}_{$threadRow->id}%");
                    $postsRowset = $postsModel->fetchAll($where);
                    foreach ($postsRowset as $postRow) {
                        $postRow->component_id = preg_replace(
                            '/^'.$threadComponentIdBefore.'/',
                            $threadRow->component_id,
                            $postRow->component_id
                        );
                        $postRow->save();
                    }
                }
                $ret['threadMoved'] = true;
                $ret['groupUrl'] = $redirectUrl;
            } else {
                // gruppen rekursiv holen
                $ret['groups'] = $this->_getGroupsRecursive(null);
                $ret['groupsTemplate'] = Vpc_Admin::getComponentFile(get_class($this), 'Groups', 'tpl');
            }
        }

        return $ret;
    }

    private function _getGroupsRecursive($parentId = null) {
        $t = new Vpc_Forum_Group_Model();
        if (is_null($parentId)) {
            $where = array('parent_id IS NULL');
        } else {
            $where = array('parent_id = ?' => $parentId);
        }
        $where[] = 'visible = 1';
        $where['component_id = ?'] = $this->getForumComponent()->getDbId();
        $rowset = $t->fetchAll($where, 'pos ASC');

        $rows = $rowset->toArray();
        foreach ($rows as $k => $row) {
            $rows[$k]['moveUrl'] = $_SERVER['REQUEST_URI'].'?to='.$row['id'];
            $rows[$k]['children'] = $this->_getGroupsRecursive($row['id']);
        }
        return $rows;
    }

    public function getGroupComponent()
    {
        return $this->getParentComponent()->getGroupComponent();
    }
    public function getForumComponent()
    {
        return $this->getParentComponent()->getGroupComponent()->getForumComponent();
    }
}