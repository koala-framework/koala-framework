<?php
class Vpc_Abstract_Image_Trl_ImageData extends Vps_Data_Abstract implements Vps_Data_Vpc_ListInterface
{
    private $_size;
    private $_subComponent;

    public function __construct($size = '')
    {
        $this->_size = $size;
    }

    public function load($row)
    {
        $componentId = $row->component_id . '-' . $row->id;
        if ($this->_subComponent) {
            $componentId .= $this->_subComponent;
        }
        return $this->_getImageUrl($componentId);
    }

    protected function _getImageUrl($componentId)
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentById($componentId, array('ignoreVisible'=>true));
        if ($c->getComponent()->getRow()->own_image) {
            $row = $c->getChildComponent('-image')->getComponent()->getRow()->getParentRow('Image');
        } else {
            return $this->_getMasterImageUrl($componentId);
        }
        if ($row) {
            return $this->_createPreviewFilename($row->getFileInfo());
        }
        return null;
    }

    protected function _getMasterImageUrl($componentId)
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentById($componentId, array('ignoreVisible'=>true));
        if (!is_instance_of($c->componentClass, 'Vpc_Abstract_Image_Component')
            && !is_instance_of($c->componentClass, 'Vpc_Abstract_Image_Trl_Component')
        ) {
            //kann vorkommen bei TextImage->LinkTag->EnlargeTag wenn was anderes als EnlergeTag einstellt ist
            return null;
        }
        $row = $c->chained->getComponent()->getRow();
        if (!$row) return null;
        $row = $row->getParentRow('Image');
        if (!$row) return null;
        return $this->_createPreviewFilename($row->getFileInfo());
    }

    private function _createPreviewFilename($info)
    {
        return "/vps/media/upload/preview?uploadId=$info[uploadId]&hashKey=$info[hashKey]&size=".$this->_size;
    }

    public function setSubComponent($key)
    {
        $this->_subComponent = $key;
    }
    public function getSubComponent()
    {
        return $this->_subComponent;
    }
}
