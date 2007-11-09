<?php
class Vpc_Basic_Download_Admin extends Vpc_Basic_Image_Admin
{
    public function setup()
    {
        $fields['filename'] = 'varchar(255) NOT NULL';
        $fields['infotext'] = 'text';
        $fields['vps_upload_id'] = 'int';
        $this->createFormTable('vpc_basic_download', $fields);
    }
}
