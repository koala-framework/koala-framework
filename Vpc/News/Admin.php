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
        if (!$this->_tableExists($tablename)) {
            $this->_db->query("CREATE TABLE IF NOT EXISTS `vpc_news` (
  `id` smallint(6) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL,
  `component_key` varchar(255) collate utf8_unicode_ci NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `teaser` text collate utf8_unicode_ci NOT NULL,
  `publish_date` date NOT NULL,
  `expiry_date` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        }
    }
}
