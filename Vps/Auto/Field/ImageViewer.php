<?php
class Vps_Auto_Field_ImageViewer extends Vps_Auto_Field_SimpleAbstract
{
    protected $_xtype = 'imageviewer';

    public function load($row)
    {
        $data = array();
        $data['imageUrl'] = $this->getImageUrl();
        $data['previewUrl'] = $this->getPreviewUrl();
        return array($this->getName() => $data);
    }
}
