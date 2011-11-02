<?php
class Vps_Update_35007 extends Vps_Update
{
    protected $_tags = array('vpc');
    public function update()
    {
        $db = Vps_Registry::get('db');
        $db->query('ALTER TABLE `vps_enquiries` ADD  `component_id` VARCHAR( 200 ) NOT NULL AFTER  `id`');
        $db->query('ALTER TABLE `vps_enquiries` ADD INDEX (  `component_id` ) ;');
        $q = $db->query("SELECT id, serialized_mail_vars FROM vps_enquiries");
        while($row = $q->fetch()) {
            if (preg_match('#^{"([-_a-zA-Z0-9]+)-paragraphs-\d+"#', $row['serialized_mail_vars'], $m)) {
                $db->query("UPDATE vps_enquiries SET component_id=? WHERE id='".((int)$row['id'])."'", $m[1]);
            }
        }
    }
}
