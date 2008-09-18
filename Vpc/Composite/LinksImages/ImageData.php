<?php
class Vpc_Composite_LinksImages_ImageData extends Vps_Data_Vpc_Image
{
    public function load($row)
    {
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename');
        $table = new $tablename(array('componentClass'=>$this->_class));
        $componentId = $row->component_id . '-' . $row->id . '-image';
        $row = $table->find($componentId)->current();
        if ($row && $row->vps_upload_id) {
            return '/vps/media/upload/preview?uploadId='.$row->vps_upload_id.'&size='.$this->_size;
        } else {
            return '';
        }
    }
}
