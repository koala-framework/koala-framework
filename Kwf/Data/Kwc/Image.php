<?php
class Kwf_Data_Kwc_Image extends Kwf_Data_Abstract implements Kwf_Data_Kwc_ListInterface
{
    protected $_class;
    protected $_size;
    private $_subComponent;

    public function __construct($class, $size)
    {
        $this->_class = $class;
        $this->_size = $size;
    }

    public function load($row, array $info = array())
    {
        $model = Kwc_Abstract::createOwnModel($this->_class);
        $componentId = $row->component_id . '-' . $row->id;
        if ($this->_subComponent) {
            $componentId .= $this->_subComponent;
        }
        $row = $model->getRow($componentId);
        if ($row && $row->kwf_upload_id) {
            $hashKey = Kwf_Util_Hash::hash($row->kwf_upload_id);
            return '/kwf/media/upload/preview?uploadId='.$row->kwf_upload_id.
                   '&hashKey='.$hashKey.'&size='.$this->_size;
        } else {
            return '';
        }
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
