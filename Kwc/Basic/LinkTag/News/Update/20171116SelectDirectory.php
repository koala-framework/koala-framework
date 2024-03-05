<?php
class Kwc_Basic_LinkTag_News_Update_20171116SelectDirectory extends Kwf_Update
{
    protected $_tags = array('kwc');

    public function update()
    {
        parent::update();

        $db = Zend_Registry::get('db');
        if ($db->fetchAll('SHOW TABLES LIKE "kwc_basic_link_news"')) {
            if (!$db->fetchAll('SHOW columns FROM kwc_basic_link_news LIKE "directory_component_id"')) {
                $db->query("ALTER TABLE `kwc_basic_link_news` ADD `directory_component_id` VARCHAR(255) NOT NULL AFTER `component_id`");
            }
            foreach ($db->fetchAll("SELECT component_id, news_id FROM kwc_basic_link_news") as $row) {
                $directoryId = $db->fetchOne("SELECT component_id FROM kwc_news WHERE id={$row['news_id']}");
                if ($directoryId) {
                    $db->query("UPDATE kwc_basic_link_news SET directory_component_id='$directoryId' WHERE component_id='{$row['component_id']}'");
                }
            }
        }
    }
}
