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
        $dao = $this->_component->getDao();
        return new Vpc_Decorator_CheckLogin_Component($dao, $page);
    }
}