<?php
class Vps_Data_Vpc_Image extends Vps_Data_Abstract
{
    protected $_class;
    protected $_size;

    public function __construct($class, $size)
    {
        $this->_class = $class;
        $this->_size = $size;
    }

    public function load($row)
    {
        $model = Vpc_Abstract::createModel($this->_class);
        $componentId = $row->component_id . '-' . $row->id;
        $row = $model->getRow($componentId);
        if ($row && $row->vps_upload_id) {
            $hashKey = md5($row->vps_upload_id.Vps_Uploads_Row::HASH_KEY);
            return '/vps/media/upload/preview?uploadId='.$row->vps_upload_id.
                   '&hashKey='.$hashKey.'&size='.$this->_size;
        } else {
            return '';
        }
    }
}
