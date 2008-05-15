<?php
class Vpc_News_Details_TreeCache extends Vpc_TreeCache_Static
{
    protected $_classes = array();

    protected function _init()
    {
        parent::_init();
        $cls = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        $this->_classes['content'] = array(
            'componentClass' => $cls['content'],
            'dbIdShortcut' => 'news_'
        );
    }
}
