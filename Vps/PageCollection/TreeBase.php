<?php
class Vps_PageCollection_TreeBase extends Vps_PageCollection_Tree
{
    public static function getInstance()
    {
        if (null === self::$_instance) {
            $dao = Zend_Registry::get('dao');
            $dao->setInvisibleMode(true);
            self::$_instance = new Vps_PageCollection_TreeBase($dao);
        }
        return self::$_instance;
    }

    public function getUrl($page)
    {
        return '/component/show/' . get_class($page) . '/' . $page->getId() . '/';
    }

    protected function _addDecorators(Vpc_Interface $page)
    {
        return $page;
    }
}