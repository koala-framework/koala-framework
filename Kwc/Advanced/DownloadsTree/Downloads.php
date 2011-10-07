<?php
class Vpc_Advanced_DownloadsTree_Downloads extends Vps_Model_Db implements Vps_Media_Output_IsValidInterface
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

    public static function isValidMediaOutput($id, $type, $className)
    {
        $componentId = substr($id, 0, strrpos($id, '_'));
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($componentId);
        if (!$c) return self::INVALID;
        while($c) {
            foreach (Vpc_Abstract::getSetting($c->componentClass, 'plugins') as $p) {
                if (is_instance_of($p, 'Vps_Component_Plugin_Interface_Login')) {
                    $p = new $p($c->componentId);
                    if (!$p->isLoggedIn()) {
                        return self::ACCESS_DENIED;
                    } else {
                        return self::VALID_DONT_CACHE;
                    }
                }
            }
            if ($c->isPage) break;
            $c = $c->parent;
        }
        return self::VALID;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $uploadId = substr($id, strrpos($id, '_')+1);
        $row = Vps_Model_Abstract::getInstance($className)->getRow($uploadId);
        $file = $row->getParentRow($type);
        return array(
            'file'=>$file->getFileSource(),
            'mimeType' => 'application/octet-stream'
        );
    }
}
