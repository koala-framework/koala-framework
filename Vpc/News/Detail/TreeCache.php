<?php
class Vpc_News_Detail_TreeCache extends Vpc_TreeCache_Static
{
    protected function _init()
    {
        $this->_classes = array('content'=>array(
            'childClassKey' => 'content',
            'dbIdShortcut' => new Zend_Db_Expr("CONCAT('news_', tc.tag)")
        ));
        parent::_init();
    }
}
