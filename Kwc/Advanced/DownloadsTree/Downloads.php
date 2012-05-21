<?php
class Kwc_Advanced_DownloadsTree_Downloads extends Kwf_Model_Db implements Kwf_Media_Output_IsValidInterface
{
    protected $_table = 'kwc_downloadstree_downloads';
    protected $_referenceMap = array(
        'File' => array(
            'refModelClass' => 'Kwf_Uploads_Model',
            'column' => 'kwf_upload_id'
        ),
        'Project' => array(
            'refModelClass' => 'Kwc_Advanced_DownloadsTree_Projects',
            'column' => 'project_id'
        ),
    );

    public static function isValidMediaOutput($id, $type, $className)
    {
        $componentId = substr($id, 0, strrpos($id, '_'));
        return Kwf_Media_Output_Component::isValid($componentId);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $uploadId = substr($id, strrpos($id, '_')+1);
        $row = Kwf_Model_Abstract::getInstance($className)->getRow($uploadId);
        $file = $row->getParentRow($type);
        return array(
            'file'=>$file->getFileSource(),
            'mimeType' => 'application/octet-stream'
        );
    }
}
