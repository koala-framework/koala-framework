<?php
class Vpc_Abstract_Image_Trl_Form_ImageData extends Vps_Data_Abstract
{
    public function load($row)
    {
        $ret = '';
        $c = Vps_Component_Data_Root::getInstance()->getComponentById($row->id, array('ignoreVisible'=>true));
        $row = $c->chained->getComponent()->getRow()->getParentRow('Image');
        if ($row) {
            $info = $row->getFileInfo();
            $src = "/vps/media/upload/preview?uploadId=$info[uploadId]&hashKey=$info[hashKey]";
            $ret = "<img src=\"$src\" />";
        }
        return $ret;
    }
}

class Vpc_Abstract_Image_Trl_Form extends Vpc_Abstract_Composite_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_ShowField('image', trlVps('Image')))
            ->setData(new Vpc_Abstract_Image_Trl_Form_ImageData());
    }
}
