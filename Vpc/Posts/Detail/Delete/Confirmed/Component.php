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
        $posts = $this->getData()->parent->parent->parent;
        return $posts->countChildComponents(array('generator'=>'detail'));
    }

    public function postProcessInput($postData)
    {
        if ($this->getData()->parent->parent->getComponent()->mayEditPost()) {
            $posts = $this->getData()->parent->parent->parent;
            $numPosts = $this->_getNumPosts();
            $post = $this->getData()->parent->parent;
            $post->row->delete();
            if ($numPosts == 1) {
                //thread auch löschen
                $posts->parent->row->delete();
            }
        }
    }
}
