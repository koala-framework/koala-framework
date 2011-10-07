<?php
class Kwc_Composite_LinksImages_ImageData extends Kwf_Data_Kwc_Image
{
    public function load($row)
    {
        $model = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->_class, 'ownModel'));
        $componentId = $row->component_id . '-' . $row->id . '-image';
        $row = $model->getRow($componentId);
        if ($row && $row->kwf_upload_id) {
            return '/kwf/media/upload/preview?uploadId='.$row->kwf_upload_id.'&size='.$this->_size;
        } else {
            return '';
        }
    }
}
