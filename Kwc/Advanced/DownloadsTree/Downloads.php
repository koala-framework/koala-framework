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
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($componentId);
        if (!$c) return self::INVALID;
        while($c) {
            foreach (Kwc_Abstract::getSetting($c->componentClass, 'plugins') as $p) {
                if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_Login')) {
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
        $row = Kwf_Model_Abstract::getInstance($className)->getRow($uploadId);
        $file = $row->getParentRow($type);
        return array(
            'file'=>$file->getFileSource(),
            'mimeType' => 'application/octet-stream'
        );
    }
}
