<?php
class Vps_PageCollection_TreeBase extends Vps_PageCollection_Tree
{
    public function getUrl($page)
    {
        return '/component/show/' . get_class($page) . '/' . $page->getId() . '/';
    }

    protected function _addDecorators(Vpc_Interface $page)
    {
        return $page;
    }
}