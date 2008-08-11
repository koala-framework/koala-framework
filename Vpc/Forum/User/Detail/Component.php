<?php
class Vpc_Forum_User_Detail_Component extends Vpc_User_Detail_Component  
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $ret['userPosts'] = 12;
        $ret['userThreads'] = 4;

        //$ret['forumUserData']['avatarUrl'] = $this->getData()->row->getFileUrl('Avatar', 'avatar');

        $ret['lastThreads'] = array();
/*
        $ret['lastPosts'] = array();
        if ($ret['userPosts']) {
            // todo: komponente holen un getTable() machen => auf treeComponentCache warten
            $postsTable = new Vpc_Posts_Model(array('componentClass' => ''));
            $where = array('user_id = ?' => $userId);
            $i = 0;
            foreach ($postsTable->fetchAll($where, 'id DESC', 20) as $row) {
                $threadComponent = $pc->getComponentById($row->component_id);
                if (preg_match('/([0-9]+)-guestbook$/', $row->component_id, $cmatches)) {
                    if (!Zend_Registry::get('userModel')->find($cmatches[1])->current()) {
                        continue;
                    }
                }

                if ($threadComponent instanceof Vpc_Forum_Posts_Directory_Component) {
                    if ($threadComponent instanceof Vpc_Forum_User_View_Guestbook_Component) {
                        $ret['lastPosts'][] = array(
                            'subject'     => 'Gästebuch: '.$threadComponent->getName(),
                            'create_time' => $row->create_time,
                            'url'         => $threadComponent->getUrl()
                        );
                    } else {
                        $ret['lastPosts'][] = array(
                            'subject'     => 'Forum: '.$threadComponent->getName(),
                            'create_time' => $row->create_time,
                            'url'         => $threadComponent->getUrl()
                        );
                    }

                    $i++;
                    if ($i >= 9) break;
                }
            }
        }
*/
        $ret['rating'] = 3;
        return $ret;
    }
}