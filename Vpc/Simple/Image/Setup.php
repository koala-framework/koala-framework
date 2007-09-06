<?php
class Vpc_Simple_Image_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $this->copyTemplate('Simple/Image.html');
        
        $config = Zend_Registry::get('config');
        $uploadDir = $config->uploads;
        if (!is_dir($uploadDir . 'SimpleImage/')) {
            mkdir($uploadDir . 'SimpleImage/');
        }
        
        $fields['name'] = 'varchar(255) NOT NULL';
        $fields['width'] = 'int(11) NOT NULL';
        $fields['height'] = 'int(11) NOT NULL';
        $fields['style'] = 'varchar(255) NOT NULL';
        $fields['color'] = 'varchar(255) NOT NULL';
        $this->createTable('vpc_simple_image', $fields);
    }

    public function deleteEntry($pageId, $componentKey)
    {
        $where = array();
        $where['page_id = ?'] = $pageId;
        $where['component_key = ?'] = $componentKey;
        $table = new Vpc_Simple_Image_IndexModel(array('db'=>$this->_db));
        $row = $table->fetchAll($where)->current();
        if ($row) {
            $uploadId = $row->vps_upload_id;
            $table->delete($where);
            $table2 = new Vps_Dao_File(array('db'=>$this->_db));
            $table2->deleteFile($uploadId);
        }
    }
}
