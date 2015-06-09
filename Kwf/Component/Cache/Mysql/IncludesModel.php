<?php
class Kwf_Component_Cache_Mysql_IncludesModel extends Kwf_Model_Db
{
    protected $_table = 'cache_component_includes';

    protected function _updateModelObserver($options, array $ids = null)
    {
    }
}
