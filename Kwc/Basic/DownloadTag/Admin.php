<?php
class Kwc_Basic_DownloadTag_Admin extends Kwc_Basic_LinkTag_Abstract_Admin
{
    public function setup()
    {
        $fields['filename'] = 'varchar(255) NOT NULL';
        $fields['kwf_upload_id'] = 'int';
        $this->createFormTable('kwc_basic_downloadtag', $fields);
    }
}
