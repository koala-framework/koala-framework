<?php
class Vpc_News_Detail_Abstract_TreeCache extends Vpc_Abstract_Composite_TreeCache
{
    protected function _init()
    {
        parent::_init();
        $this->_classes['content']['dbIdShortcut'] = new Zend_Db_Expr("CONCAT('news_', tc.tag, '-content')");
    }

}
