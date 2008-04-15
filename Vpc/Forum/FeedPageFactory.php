<?php
class Vpc_Forum_FeedPageFactory extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array(
        array(
            'componentClass' => 'Vpc_Forum_LatestThreads_Feed_Component',
            'showInMenu' => false,
            'name' => 'Feed',
            'id' => 'feed'
        )
    );
}