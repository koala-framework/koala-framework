<?php
class Vpc_Forum_User_View_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['tablename'] = 'Vpc_Forum_User_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $userId = $this->getCurrentPageKey();

        $userData = Zend_Registry::get('userModel')->find($userId)->current();
        $forumUserData = $this->getTable()->find($userId)->current();

        if (!$userData) {
            throw new Vps_ClientException(trlVps('User does not exist (anymore).'));
        }
        if (!$forumUserData) {
            throw new Vps_ClientException(trlVps('Forum-User does not exist (anymore).'));
        }

        $ret['userData'] = $userData->toArray();
        $ret['userPosts'] = $forumUserData->getNumPosts();
        $ret['userThreads'] = $forumUserData->getNumThreads();
        $ret['forumUserData'] = $forumUserData->toArray();

        $ret['forumUserData']['avatarUrl'] = '';
        if ($forumUserData && $forumUserData->avatar) {
            $ret['forumUserData']['avatarUrl'] = $forumUserData->getFileUrl('Avatar', 'avatar');
        }

        $pc = $this->getPageCollection();

        $ret['lastThreads'] = array();
        if ($ret['userThreads']) {
            $threadTable = new Vpc_Forum_Thread_Model();
            $where = array('user_id = ?' => $userId);
            foreach ($threadTable->fetchAll($where, null, 3) as $row) {
                $groupPage = $pc->getComponentById($row->component_id);
                if (!$groupPage) continue;

                $groupPageFactory = $groupPage->getPageFactory();
                if (!$groupPageFactory) continue;

                $child = $groupPageFactory->getChildPageByRow($row);
                if (!$child) continue;

                $ret['lastThreads'][] = array(
                    'subject'     => $row->subject,
                    'create_time' => $row->create_time,
                    'url'         => $child->getUrl()
                );
            }
        }

        $ret['lastPosts'] = array();
        if ($ret['userPosts']) {
            $postsTable = new Vpc_Posts_Model(array('componentClass' => ''));
            $where = array('user_id = ?' => $userId);
            $i = 0;
            foreach ($postsTable->fetchAll($where, 'id DESC', 10) as $row) {
                $threadComponent = $pc->getComponentById($row->component_id);
                if ($threadComponent instanceof Vpc_Forum_Posts_Component) {
                    $ret['lastPosts'][] = array(
                        'subject'     => $threadComponent->getName(),
                        'create_time' => $row->create_time,
                        'url'         => $threadComponent->getUrl()
                    );
                    $i++;
                    if ($i >= 3) break;
                }
            }
        }

        return $ret;
    }
}