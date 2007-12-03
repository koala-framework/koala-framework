<?php
class Vpc_News_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vpc.News.Panel';
    }
    
    public function getControllerConfig($pageId, $componentKey)
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        return array('contentClass' => $classes['details']);
    }

    public function setup()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        Vpc_Admin::getInstance($classes['content'])->setup();

        $fields['publish_date'] = "datetime default NULL";
        $fields['expiry_date'] = "datetime default NULL";
        $this->createFormTable('vpc_news', $fields);
    }

    public function delete($pageId, $componentKey)
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        Vpc_Admin::getInstance($classes['content'])->delete($pageId, $componentKey . '-content');
        parent::delete($pageId, $componentKey);
    }
}
