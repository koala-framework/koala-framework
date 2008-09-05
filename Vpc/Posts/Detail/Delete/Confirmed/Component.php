<?php
class Vpc_Posts_Detail_Delete_Confirmed_Component extends Vpc_Posts_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlVps('Comment was successfully deleted.');
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    protected function _getTargetPage()
    {
        $ret = $this->getData()->getParentPage()->getParentPage();
        if ($this->_getNumPosts() == 1) {
            $ret = $ret->getParentPage();
        }
        return $ret;
    }

    private function _getNumPosts()
    {
        $posts = $this->getData()->parent->parent->parent->parent;
        return $posts->countChildComponents(array('generator'=>'detail'));
    }

    public function postProcessInput($postData)
    {
        $actions = $this->getData()->parent->parent;
        if ($actions->getComponent()->mayEditPost()) {
            $post = $actions->parent;
            $post->row->delete();
            $numPosts = $this->_getNumPosts();
            if ($numPosts == 0) {
                //thread auch löschen
                $post->parent->parent->row->delete();
            }
        }
    }
}
