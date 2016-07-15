<?php
class Kwf_Update_20160715KwfUpdates extends Kwf_Update
{
    public function update()
    {
        $db = Kwf_Registry::get('db');

        $doneNames = Kwf_Util_Update_Helper::getExecutedUpdatesNames();

        $db->query("CREATE TABLE `kwf_updates` (
            `id` int(11) NOT NULL,
            `name` varchar(255) NOT NULL,
            `executed_at` datetime DEFAULT NULL,
            `log` TEXT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $db->query("ALTER TABLE `kwf_updates`
            ADD PRIMARY KEY (`id`);");

        $db->query("ALTER TABLE `kwf_updates`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");

        foreach ($doneNames as $name) {
            $db->query("INSERT INTO kwf_updates SET name=?, executed_at=NOW()", $name);
        }

        if (in_array('kwf_update', $db->listTables())) {
            $db->query("RENAME TABLE `kwf_update` TO `kwf_update_backup`");
        }
    }
}
