<?php
class Vpc_Basic_Download_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Index.html', 'Basic/Download.html');

        $fields['name'] = 'varchar(255) NOT NULL';
        $fields['info'] = 'text';
        $fields['vps_upload_id'] = 'int';
        if ($this->createTable('vpc_Basic_download', $fields)) {
            $this->_db->query('ALTER TABLE vpc_basic_download ADD INDEX (vps_upload_id)');
            $this->_db->query('ALTER TABLE vpc_basic_download
                ADD FOREIGN KEY (vps_upload_id)
                REFERENCES vps_uploads (id)
                ON DELETE SET NULL ON UPDATE SET NULL');
        }
    }

    public function delete($component)
    {
        $row = $this->_getRow($component);
        if ($row) {
            $fileTable = $component->getTable('Vps_Dao_File');
            $fileTable->deleteFile($row->vps_upload_id);
        }
        parent::delete($component);
    }
}
