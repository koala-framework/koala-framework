<?php
class Vpc_Basic_Download_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $this->copyTemplate('Basic/Download.html');
        
        $config = Zend_Registry::get('config');
        $uploadDir = $config->uploads;
        if (!is_dir($uploadDir . 'BasicDownload/')) {
            mkdir($uploadDir . 'BasicDownload/');
        }
        
        $fields['name'] = 'varchar(255) NOT NULL';
        $fields['info'] = 'text';
        $fields['vps_upload_id'] = 'int';
        $this->createTable('vpc_Basic_download', $fields);
        $this->_db->query('ALTER TABLE vpc_basic_download ADD INDEX (vps_upload_id)');
        $this->_db->query('ALTER TABLE vpc_basic_download
            ADD FOREIGN KEY (vps_upload_id)
            REFERENCES vps_uploads (id)
            ON DELETE SET NULL ON UPDATE SET NULL');
    }

    public function deleteEntry($pageId, $componentKey)
    {
        $where = array();
        $where['page_id = ?'] = $pageId;
        $where['component_key = ?'] = $componentKey;
        $table = new Vpc_Basic_Download_IndexModel(array('db'=>$this->_db));
        $row = $table->fetchAll($where)->current();
        if ($row) {
            $uploadId = $row->vps_upload_id;
            $table->delete($where);
            $table2 = new Vps_Dao_File(array('db'=>$this->_db));
            $table2->deleteFile($uploadId);
        }
    }
}
