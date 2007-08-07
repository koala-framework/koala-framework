<?php
class Vps_PageCollection_TreeBase extends Vps_PageCollection_Tree
{
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new Vps_PageCollection_TreeBase(Zend_Registry::get('dao'));
        }
        return self::$_instance;
    }

    public function getUrl($page)
    {
        return '/component/show/' . $page->getId() . '/';
    }

    protected function _addDecorators(Vpc_Interface $page)
    {
        return $page;
    }
}