<?php
class Vpc_Basic_DownloadTag_Admin extends Vpc_Basic_LinkTag_Abstract_Admin
{
    public function setup()
    {
        $fields['filename'] = 'varchar(255) NOT NULL';
        $fields['vps_upload_id'] = 'int';
        $this->createFormTable('vpc_basic_downloadtag', $fields);
    }
}
