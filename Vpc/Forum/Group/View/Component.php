<?php
class Vpc_Forum_Group_View_Component extends Vpc_Directories_List_ViewPage_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach ($ret['items'] as &$item) {
            $posts = $item->getChildComponents(array('generator' => 'detail'));
            $item->firstPost = array_shift($posts);
            $item->lastPost = array_pop($posts);
            $item->firstUser = $this->getData()->parent->parent->getChildComponent('_users')->getChildComponent('_' . $item->firstPost->row->user_id);
            $item->lastUser = $this->getData()->parent->parent->getChildComponent('_users')->getChildComponent('_' . $item->lastPost->row->user_id);
            $item->replies = count($posts) - 1;
        }
        return $ret;
    }
}
