<?php
class Vpc_News_PageFactoryFeed extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array(array(
        'id'=>'feed',
        'name'=>'Feed',
        'showInMenu' => false,
        'componentClass' => 'Vpc_News_List_Feed_Component'
    ));
    protected $_additionalFactories = array();
}
