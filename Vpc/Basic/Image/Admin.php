<?php
class Vpc_Basic_Image_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Template.html', 'Basic/Image.html');

        $fields['name'] = 'varchar(255) NOT NULL';
        $fields['width'] = 'int(11) NOT NULL';
        $fields['height'] = 'int(11) NOT NULL';
        $fields['style'] = 'varchar(255) NOT NULL';
        $fields['vps_upload_id'] = 'int';
        if ($this->createTable('vpc_basic_image', $fields)) {
            $this->_db->query('ALTER TABLE vpc_basic_image ADD INDEX (vps_upload_id)');
            $this->_db->query('ALTER TABLE vpc_basic_image
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
