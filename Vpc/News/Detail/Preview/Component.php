<?php
class Vpc_News_Detail_Preview_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $news = $this->getNewsComponent();
        $ret['detail']  = $this->getTreeCacheRow()->getTable()
                    ->find($news->component_id.'_'.
                            $this->getTreeCacheRow()->tag)->current();
        $ret['news'] = $news->getComponent()
                            ->getNewsRow($this->getTreeCacheRow()->tag);
        $ret['previewImage'] = $news->component_id.'_'.
                            $this->getTreeCacheRow()->tag.'-image';
        return $ret;
    }


    public function getNewsComponent()
    {
        return $this->getTreeCacheRow()
            ->findParentComponent()
            ->getComponent()->getNewsComponent();
    }
}
