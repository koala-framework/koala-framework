<?php
class Vpc_News_PageFactoryCategories extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array(array(
        'id'=>'categories',
        'name'=>'Kategorien',
        'componentClass'=>'Vpc_News_Categories_Component',
        'showInMenu' => false
    ));
    protected $_additionalFactories = array();

    protected function _createStaticPage($p)
    {
        $page = parent::_createStaticPage($p);
        while ($page instanceof Vpc_Decorator_Abstract) {
            $page = $page->getComponent();
        }
        return $page;
    }
}
