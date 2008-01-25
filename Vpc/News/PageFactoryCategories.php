<?php
class Vpc_News_PageFactoryCategories extends Vpc_Abstract_StaticPageFactory implements Vpc_News_Interface_PageFactoryCategory
{
    protected $_pages = array(array(
        'id'=>'categories',
        'name'=>'Kategorien',
        'showInMenu' => false
    ));
    protected $_additionalFactories = array();

    public function setComponentClass($class)
    {
        $this->_pages[0]['componentClass'] = $class;
    }

    protected function _createStaticPage($p)
    {
        $page = parent::_createStaticPage($p);
        while ($page instanceof Vpc_Decorator_Abstract) {
            $page = $page->getComponent();
        }
        return $page;
    }
}
