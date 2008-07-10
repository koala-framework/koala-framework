<?php
class Vpc_News_Directory_TreeCacheMenuBox extends Vps_Component_Generator_StaticBox
{
    protected $_classes = array('submenu' =>
        array(
            'componentClass' => 'Vpc_News_Menu_Component',
            'priority' => 3
        )
    );
}
