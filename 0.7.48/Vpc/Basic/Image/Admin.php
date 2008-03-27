<?php
class Vpc_Basic_Image_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['filename'] = 'varchar(255) DEFAULT NULL';
        $fields['width'] = 'int(11) DEFAULT NULL';
        $fields['height'] = 'int(11) DEFAULT NULL';
        $fields['scale'] = 'varchar(255) DEFAULT NULL';
        $fields['enlarge'] = 'tinyint(3) DEFAULT 0';
        $fields['vps_upload_id'] = 'int(11) DEFAULT NULL';
        $this->createFormTable('vpc_basic_image', $fields);
    }
}
