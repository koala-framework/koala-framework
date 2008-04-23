<?php
class Vpc_Forum_Posts_Post_Component extends Vpc_Posts_Post_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['user'] = 'Vpc_Forum_Posts_Post_UserDetail_Component';
        return $ret;
    }

    public function getForumComponent()
    {
        return $this->getParentComponent()->getForumComponent();
    }

    public function getGroupComponent()
    {
        return $this->getParentComponent()->getGroupComponent();
    }

    public function getThreadComponent()
    {
        return $this->getParentComponent()->getParentComponent();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $post = $this->getTable()->find($this->getCurrentComponentKey())->current();

        $ret['signature'] = '';
        $ret['avatarUrl'] = '';
        $ret['userUrl']   = '';
        $ret['deleteUrl'] = '';

        if ($this->mayModeratePost()) {
            // post löschen
            if (!empty($_GET['deletePost']) && $_GET['deletePost'] == $this->getCurrentComponentKey()) {
                $deleteRow = $this->getTable()->find($_GET['deletePost'])->current();
                if ($deleteRow) {
                    $locationHeader = $_SERVER['REQUEST_URI'];
                    // Prüfen ob noch beiträge in diesem thread, sonst thread auch löschen
                    if ($this->getTable()->fetchAll(
                        array('component_id = ?' => $deleteRow->component_id)
                    )->count() <= 1) {
                        $threadVars = $this->getThreadComponent()->getThreadVars();
                        $threadModel = new Vpc_Forum_Thread_Model();
                        $threadRow = $threadModel->find($threadVars['thread_id'])->current();
                        if ($threadRow) {
                            $locationHeader = $this->getGroupComponent()->getUrl();
                            $threadDeleted = $threadRow->delete();
                        }
                    }
                    // post wirklich löschen
                    if ($deleteRow->delete()) {
                        header('Location: '.$locationHeader);
                        exit;
                    }
                }
            }

            // lösch-url generieren
            $ret['deleteUrl'] = $this->getUrl().'?deletePost='.$this->getCurrentComponentKey();
        }

        $forumUserModel = new Vpc_Forum_User_Model();
        $user = $forumUserModel->find($post->user_id)->current();
        if ($user) {
            $userViewComponent = $this->getForumComponent()->getUserViewComponent($user);
            if ($userViewComponent) $ret['userUrl'] = $userViewComponent->getUrl();

            $ret['signature'] = $user->signature;

            if ($user->avatar) {
                $ret['avatarUrl'] = $user->getFileUrl('Avatar', 'avatarsmall');
            }
        }

        $ret['writeUrl'] = $this->getParentComponent()->getPageFactory()->getChildPageById('write')->getUrl()
            .'?quote='.$this->getId();
        $pagingKey = $this->getParentComponent()->getId().'-paging';
        if (isset($_GET[$pagingKey])) {
            $ret['writeUrl'] .= '&'.$pagingKey.'='.$_GET[$pagingKey];
        }

        return $ret;
    }

    // deprecated for compatibility
    public function mayModeratePost()
    {
        return $this->getGroupComponent()->mayModerate();
    }

    public function mayEditPost()
    {
        $ret = parent::mayEditPost();
        if (!$ret) return $this->mayModeratePost();
        return $ret;
    }
}
