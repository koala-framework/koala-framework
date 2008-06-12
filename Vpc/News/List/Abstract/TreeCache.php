<?php
class Vpc_News_List_Abstract_TreeCache extends Vpc_TreeCache_Static
{
    protected $_additionalTreeCaches = array(
        'Vpc_News_List_Abstract_TreeCachePreview'
    );

    protected $_classes = array('paging'=>array(
        'childClassKey' => 'paging',
    ));

}
