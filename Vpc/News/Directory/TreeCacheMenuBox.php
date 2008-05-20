<?php
class Vpc_News_Directory_TreeCacheMenuBox extends Vpc_TreeCache_StaticBox
{
    protected $_classes = array(
        array(
            'id' => 'submenu',
            'componentClass' => 'Vpc_News_Menu_Component',
            'priority' => 3
        )
    );
}
