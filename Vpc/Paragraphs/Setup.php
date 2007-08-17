<?php
class Vpc_Paragraphs_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $tablename = 'component_paragraphs';
        if (!$this->_tableExits($tablename)) {
          $this->_db->query("CREATE TABLE `$tablename` (
                  `id` int(10) unsigned NOT NULL auto_increment,
                  `page_id` int(10) unsigned NOT NULL,
                  `component_key` varchar(255) NOT NULL,
                  `component_class` varchar(255) NOT NULL,
                  `pos` smallint NOT NULL,
                  `visible` tinyint(4) NOT NULL
                   PRIMARY KEY  (`id`)
                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }

        if (!file_exists('application/views/Paragraphs.html')){
          copy(VPS_PATH . '/application/views/Paragraphs.html', 'application/views/Paragraphs.html');
        }
    }
}