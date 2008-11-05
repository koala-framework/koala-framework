<?php
class Vps_Util_Model_Welcome extends Vps_Model_Db
    implements Vps_Media_Output_Interface
{
    protected $_table = 'vps_welcome';
    protected $_referenceMap    = array(
        'WelcomeImage' => array(
            'column'           => 'vps_upload_id',
            'refModelClass'    => 'Vps_Uploads_Model',
        ),
        'LoginImage' => array(
            'column'           => 'login_vps_upload_id',
            'refModelClass'    => 'Vps_Uploads_Model',
        )
    );

    public static function getImageDimensions($type)
    {
        if ($type == 'LoginImage') {
            return array(300, 80, Vps_Media_Image::SCALE_CROP);
        } else if ($type == 'WelcomeImage') {
            return array(300, 100);
        }
    }
    public static function getMediaOutput($id, $type, $className)
    {
        $row = Vps_Model_Abstract::getInstance($className)->getRow($id);
        $dim = self::getImageDimensions($type);
        return array(
            'contents'=>Vps_Media_Image::scale($row->getParentRow($type), $dim),
            'mimeType' => $row->getParentRow($type)->mime_type
        );
    }
}
