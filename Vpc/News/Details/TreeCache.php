<?php
class Vpc_News_Details_TreeCache extends Vpc_TreeCache_Static
{
    protected $_classes = array(array(
            'childClassKey' => 'content',
            'dbIdShortcut' => 'news-'
        ));
}
