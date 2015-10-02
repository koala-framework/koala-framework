<?php
class Kwf_Update_20151002WelcomeUpload extends Kwf_Update
{
    public function update()
    {
        $db = Kwf_Registry::get('db');

        //due to a bug the 39000 update script didn't convert kwf_welcome
        //change fields if 39000 was already executed without the bugfix
        foreach ($db->query('SHOW FIELDS FROM kwf_welcome')->fetchAll() as $row) {
            if ($row['Field'] == 'kwf_upload_id' && substr($row['Type'], 0, 3) == 'int') {
                $db->query('UPDATE kwf_welcome SET kwf_upload_id=NULL');
                $db->query('UPDATE kwf_welcome SET login_kwf_upload_id=NULL');
                $db->query('ALTER TABLE  `kwf_welcome` CHANGE  `kwf_upload_id`  `kwf_upload_id` VARBINARY( 36 ) UNSIGNED NULL DEFAULT NULL ,
                    CHANGE  `login_kwf_upload_id`  `login_kwf_upload_id` VARBINARY( 36 ) UNSIGNED NULL DEFAULT NULL');
                break;
            }
        }
    }
}
