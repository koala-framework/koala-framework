<?php
class Vpc_Forum_Posts_Detail_Actions_Component extends Vpc_Posts_Detail_Actions_Component
{
    public function mayDeletePost()
    {
        $ret = parent::mayDeletePost();
        $countPosts = $this->getData()->parent->parent
            ->countChildComponents(array('generator' => 'detail'));
        if ($countPosts > 1 && $this->_isFirstPost()) $ret = false;
        return $ret;
    }
    
    private function _isFirstPost()
    {
        $firstPost = $this->getData()->parent->parent
            ->getChildComponent(array('generator' => 'detail'));
        return $firstPost->componentId == $this->getData()->parent->componentId;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if ($ret['edit'] && $this->_isFirstPost()) {
            $ret['edit'] = $this->getData()->parent->parent->parent->getChildComponent('_edit');
        }
        return $ret;
    }
}
