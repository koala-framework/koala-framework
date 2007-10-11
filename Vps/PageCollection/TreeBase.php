<?php
class Vps_PageCollection_TreeBase extends Vps_PageCollection_Tree
{
    public function __construct(Vps_Dao $dao, $urlScheme = Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL, $decoratorClasses = array())
    {
        parent::__construct($dao, $urlScheme, $decoratorClasses);
        $this->showInvisible(true);
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new Vps_PageCollection_TreeBase(Zend_Registry::get('dao'));
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