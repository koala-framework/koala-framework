<?php
class Vpc_Forum_SearchPageFactory extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array(
        array(
            'componentClass' => 'Vpc_Forum_Search_Component',
            'showInMenu' => true,
            'name' => 'Search',
            'id' => 'search'
        )
    );
}