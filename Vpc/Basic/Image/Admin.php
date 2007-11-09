<?php
class Vpc_Basic_Image_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['filename'] = 'varchar(255) NOT NULL';
        $fields['width'] = 'int(11) NOT NULL';
        $fields['height'] = 'int(11) NOT NULL';
        $fields['scale'] = 'varchar(255) NOT NULL';
        $fields['vps_upload_id'] = 'int';
        $this->createFormTable('vpc_basic_image', $fields);
    }

    public function delete($class, $pageId, $componentKey)
    {
        $row = $this->_getRow($class, $pageId, $componentKey);
        if ($row) {
            $uploadId = $row->vps_upload_id;
            $row->vps_upload_id = null;
            $row->save();
            $fileTable = new Vps_Dao_File();
            $fileTable->delete($uploadId);
        }
        parent::delete($class, $pageId, $componentKey);
    }
}
