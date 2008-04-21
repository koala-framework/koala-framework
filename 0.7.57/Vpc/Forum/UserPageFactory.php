<?php
class Vpc_Forum_UserPageFactory extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array(
        array(
            'componentClass' => 'Vpc_Forum_User_Component',
            'showInMenu' => false,
            'name' => 'Users',
            'id' => 'users'
        )
    );
}