<?php
class Vpc_Forum_Posts_Detail_Actions_Delete_Confirmed_Component extends Vpc_Posts_Detail_Delete_Confirmed_Component
{
    protected function _getTargetPage()
    {
        $ret = parent::_getTargetPage();
        if ($this->_getNumPosts() == 1) {
            $ret = $ret->getParentPage();
        }
        return $ret;
    }

    public function postProcessInput($postData)
    {
        parent::postProcessInput($postData);
        $numPosts = $this->_getNumPosts();
        if ($numPosts == 0) {
            //thread auch löschen
            $this->getData()->parent->parent->parent->parent->parent->row->delete();
        }
    }

    private function _getNumPosts()
    {
        $posts = $this->getData()->parent->parent->parent->parent;
        return $posts->countChildComponents(array('generator'=>'detail'));
    }
}
