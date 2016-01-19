<?php
class Kwc_Root_Category_Update_20150818PagesHistory extends Kwf_Update
{
    public function update()
    {
        $db = Kwf_Registry::get('db');
        if (in_array('kwf_pages_history', $db->listTables())) {
            return;
        }
        $db->query("CREATE TABLE IF NOT EXISTS `kwf_pages_history` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `page_id` int(10) unsigned NOT NULL,
            `parent_id` varchar(255) NOT NULL,
            `filename` varchar(255) NOT NULL,
            `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `page_id` (`page_id`),
            KEY `parent_id` (`parent_id`),
            KEY `filename` (`filename`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;");

        $db->query("ALTER TABLE `kwf_pages_history`
            ADD CONSTRAINT `kwf_pages_history_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `kwf_pages` (`parent_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `kwf_pages_history_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `kwf_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
    }
}
