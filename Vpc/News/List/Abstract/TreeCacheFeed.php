<?php
class Vpc_News_List_Abstract_TreeCacheFeed extends Vpc_TreeCache_StaticPage
{
    protected $_classes = array('feed'=>array(
        'componentClass' => 'Vpc_News_List_Abstract_Feed_Component',
        'name' => 'Feed',
    ));

}
