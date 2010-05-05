<?php
class Vpc_Abstract_Image_Trl_ImageData extends Vps_Data_Abstract
{
    private $_size;

    public function __construct($size = '')
    {
        $this->_size = $size;
    }

    public function load($row)
    {
        return $this->_getImageUrl($row->component_id . '-' . $row->id);
    }

    protected function _getImageUrl($componentId)
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentById($componentId, array('ignoreVisible'=>true));
        $row = $c->chained->getComponent()->getRow()->getParentRow('Image');
        if ($row) {
            $info = $row->getFileInfo();
            return "/vps/media/upload/preview?uploadId=$info[uploadId]&hashKey=$info[hashKey]&size=".$this->_size;
        }
        return null;
    }
}
