<?php
class Vpc_Forum_Posts_Detail_Actions_Component extends Vpc_Posts_Detail_Actions_Component
{
    public function mayEditPost()
    {
        $ret = parent::mayEditPost();
        if (!$ret) {
            $component = $this->getData()->getParentPage()->getComponent();
            $ret = $component->mayModerate();
        }
        return $ret;
    }
}
