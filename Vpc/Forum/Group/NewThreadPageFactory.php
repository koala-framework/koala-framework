<?php
class Vpc_Forum_Group_NewThreadPageFactory extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array(
        array(
            'showInMenu' => false,
            'name' => 'New Thread',
            'id' => 'newthread'
        )
    );
    protected function _init()
    {
        parent::_init();
        $this->_pages[0]['componentClass'] = $this->_getChildComponentClass('newthread');
    }

    protected function _decoratePage($page)
    {
        $dao = $this->_component->getDao();
        return new Vpc_Decorator_CheckLogin_Component($dao, $page);
    }
}