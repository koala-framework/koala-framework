<?php
class Kwf_Component_Cache_Mysql_Model extends Kwf_Model_Db
{
    protected $_table = 'cache_component';

    protected function _updateModelObserver($options, array $ids = null)
    {
    }
}
