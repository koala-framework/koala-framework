<?php
class Kwc_Basic_LinkTag_Event_Update_20171116SelectEventsDirectory extends Kwf_Update
{
    protected $_tags = array('kwc');

    public function update()
    {
        parent::update();

        $db = Zend_Registry::get('db');
        if ($db->fetchAll('SHOW TABLES LIKE "kwc_basic_link_event"')) {
            if (!$db->fetchAll('SHOW columns FROM kwc_basic_link_event LIKE "directory_component_id"')) {
                $db->query("ALTER TABLE `kwc_basic_link_event` ADD `directory_component_id` VARCHAR(255) NOT NULL AFTER `component_id`");
            }
            foreach ($db->fetchAll("SELECT component_id, event_id FROM kwc_basic_link_event") as $row) {
                $directoryId = $db->fetchOne("SELECT component_id FROM kwc_events WHERE id={$row['event_id']}");
                if ($directoryId) {
                    $db->query("UPDATE kwc_basic_link_event SET directory_component_id='$directoryId' WHERE component_id='{$row['component_id']}'");
                }
            }
        }
    }
}
