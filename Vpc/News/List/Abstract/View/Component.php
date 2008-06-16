<?php
class Vpc_News_List_Abstract_View_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $parent = $this->getTreeCacheRow()->findParentComponent()->getComponent();

        $news = $parent->getNews();

        $ret['news'] = array();
        foreach ($news as $new) {
            $detail = $this->getTreeCacheRow()->getTable()
                    ->find($parent->getNewsComponent()->component_id.'_'.$new->id)->current();
            $ret['news'][] = array(
                'row' => $new,
                'detail' => $detail
            );
        }
        return $ret;
    }
}
