<?php
class Vpc_Advanced_DownloadsTree_Downloads extends Vps_Model_Db implements Vps_Media_Output_Interface
{
    protected $_table = 'vpc_downloadstree_downloads';
    protected $_referenceMap = array(
        'File' => array(
            'refModelClass' => 'Vps_Uploads_Model',
            'column' => 'vps_upload_id'
        ),
        'Project' => array(
            'refModelClass' => 'Vpc_Advanced_DownloadsTree_Projects',
            'column' => 'project_id'
        ),
    );
    public static function getMediaOutput($id, $type, $className)
    {
        $row = Vps_Model_Abstract::getInstance($className)->getRow($id);
        $file = $row->getParentRow($type);
        return array(
            'file'=>$file->getFileSource(),
            'mimeType' => 'application/octet-stream'
        );
    }
}
