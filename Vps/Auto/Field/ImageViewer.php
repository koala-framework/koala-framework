<?php
class Vps_Auto_Field_ImageViewer extends Vps_Auto_Field_Abstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('imageviewer');
    }

    public function load($row)
    {
        $data = array();
        $data['imageUrl'] = $this->getImageUrl();
        $data['previewUrl'] = $this->getPreviewUrl();
        return array($this->getFieldName() => $data);
    }
}
