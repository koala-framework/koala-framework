<?php
class Vpc_Composite_LinksImages_ImageData extends Vps_Data_Vpc_Image
{
    public function load($row)
    {
        $model = Vps_Model_Abstract::getInstance(Vpc_Abstract::getSetting($this->_class, 'ownModel'));
        $componentId = $row->component_id . '-' . $row->id . '-image';
        $row = $model->getRow($componentId);
        if ($row && $row->vps_upload_id) {
            return '/vps/media/upload/preview?uploadId='.$row->vps_upload_id.'&size='.$this->_size;
        } else {
            return '';
        }
    }
}
