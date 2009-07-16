<?php
class Vpc_Basic_Flash_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['width'] = 'int(11) DEFAULT NULL';
        $fields['height'] = 'int(11) DEFAULT NULL';
        $fields['vps_upload_id_media'] = 'int(11) DEFAULT NULL';
        $fields['flash_vars'] = 'text DEFAULT NULL';
        $this->createFormTable('vpc_basic_flash', $fields);

        $fields['width'] = 'int(11) DEFAULT NULL';
        $fields['height'] = 'int(11) DEFAULT NULL';
        $fields['vps_upload_id_media'] = 'int(11) DEFAULT NULL';
        $this->createFormTable('vpc_basic_flash_vars', $fields);
    }
}
