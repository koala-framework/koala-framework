<?php
class Kwc_Basic_LinkTag_BlogPost_Update_20160227Table extends Kwf_Update
{
    public function update()
    {
        parent::update();

        $db = Zend_Registry::get('db');
        $db->query("CREATE TABLE IF NOT EXISTS `kwc_basic_link_blog_post` (
  `component_id` varchar(255) NOT NULL,
  `blog_post_id` varchar(255) NOT NULL,
  PRIMARY KEY  (`component_id`),
  KEY `blog_post_id` (`blog_post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }
}

