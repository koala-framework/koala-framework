<?php
class Kwc_Basic_Svg_Update_20190621PrepareSvgTable extends Kwf_Update
{
    public function update()
    {
        $db = Kwf_Registry::get('db');
        $tables = $db->listTables();
        if (!in_array('kwc_basic_svgs', $tables)) {
            $db->query("CREATE TABLE IF NOT EXISTS `kwc_basic_svgs` (
                  `component_id` varchar(255) NOT NULL,
                  `kwf_upload_id` VARBINARY(36) NOT NULL,
                  PRIMARY KEY (`component_id`),
                  KEY `kwf_upload_id` (`kwf_upload_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
    }
}
