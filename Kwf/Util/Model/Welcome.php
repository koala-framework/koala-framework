<?php
class Kwf_Util_Model_Welcome extends Kwf_Model_Db
    implements Kwf_Media_Output_Interface
{
    protected $_table = 'kwf_welcome';
    protected $_rowClass = 'Kwf_Util_Model_Row_Welcome';
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
        if ($type == 'LoginImageLarge') {
            return array(350, 150, 'cover' => false);
        } else if ($type == 'LoginImage') {
            return array(300, 80, 'cover' => false);
        } else if ($type == 'WelcomeImage') {
            return array(300, 100, 'cover' => false);
        }
    }
    public static function getMediaOutput($id, $type, $className)
    {
        $row = Kwf_Model_Abstract::getInstance($className)->getRow($id);
        $dim = self::getImageDimensions($type);
        if ($type == 'LoginImageLarge') {
            $type = 'LoginImage';
        }
        $uploadRow = $row->getParentRow($type);
        return array(
            'contents'=>Kwf_Media_Image::scale($uploadRow, $dim, $uploadRow->id),
            'mimeType' => $row->getParentRow($type)->mime_type
        );
    }
}
