<?php
class Kwf_Util_Model_Welcome extends Kwf_Model_Db
    implements Kwf_Media_Output_Interface
{
    protected $_table = 'kwf_welcome';
    protected $_referenceMap    = array(
        'WelcomeImage' => array(
            'column'           => 'kwf_upload_id',
            'refModelClass'    => 'Kwf_Uploads_Model',
        ),
        'LoginImage' => array(
            'column'           => 'login_kwf_upload_id',
            'refModelClass'    => 'Kwf_Uploads_Model',
        )
    );

    public static function getImageDimensions($type)
    {
        if ($type == 'LoginImage') {
            return array(300, 80, Kwf_Media_Image::SCALE_CROP);
        } else if ($type == 'WelcomeImage') {
            return array(300, 100);
        }
    }
    public static function getMediaOutput($id, $type, $className)
    {
        $row = Kwf_Model_Abstract::getInstance($className)->getRow($id);
        $dim = self::getImageDimensions($type);
        return array(
            'contents'=>Kwf_Media_Image::scale($row->getParentRow($type), $dim),
            'mimeType' => $row->getParentRow($type)->mime_type
        );
    }
}
