<?php
class Kwf_Model_RowCache_RowCacheModel extends Kwf_Model_RowCache
{
    protected $_proxyModel = 'Kwf_Model_RowCache_SourceModel';
    protected $_cacheColumns = array('foo');
}
