<?php
class Vpc_News_TreeCache extends Vpc_TreeCache_TablePage
{
    protected $_childClassKey = 'details';
    protected $_nameColumn = 'title';

    protected function _init()
    {
        parent::_init();
    }
}
