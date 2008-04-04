<?php
class Vpc_Composite_LinksImages_ImageData extends Vps_Auto_Data_Vpc_Image
{
    public function load($row)
    {
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename');
        $table = new $tablename(array('componentClass'=>$this->_class));
        $componentId = $row->component_id . '-' . $row->id . '-image';
        $row = $table->find($componentId)->current();
        if ($row) {
            return $row->getFileUrl(null, $this->_size);
        } else {
            return '';
        }
    }
}