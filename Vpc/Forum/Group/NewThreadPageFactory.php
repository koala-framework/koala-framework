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
        $forumComponent = $this->_component->getForumComponent();
        $decoratorName = $forumComponent->getSetting(get_class($forumComponent), 'loginDecorator');

        $dao = $this->_component->getDao();
        return new $decoratorName($dao, $page);
    }
}