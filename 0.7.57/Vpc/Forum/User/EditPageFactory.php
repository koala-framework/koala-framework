<?php
class Vpc_Forum_User_EditPageFactory extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array(
        array(
            'componentClass' => 'Vpc_Forum_User_Edit_Component',
            'showInMenu' => false,
            'name' => 'Forumaccount bearbeiten',
            'id' => 'edituser'
        )
    );

    protected function _decoratePage($page)
    {
        $forumComponent = $this->_component->getForumComponent();
        $decoratorName = $forumComponent->getSetting(get_class($forumComponent), 'loginDecorator');

        $dao = $this->_component->getDao();
        return new $decoratorName($dao, $page);
    }
}