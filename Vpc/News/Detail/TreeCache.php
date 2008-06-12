<?php
class Vpc_News_Detail_TreeCache extends Vpc_News_Detail_Abstract_TreeCache
{
    protected function _init()
    {
        parent::_init();
        $this->_classes['image']['dbIdShortcut'] = new Zend_Db_Expr("CONCAT('news_', tc.tag, '-image')");
    }
}
