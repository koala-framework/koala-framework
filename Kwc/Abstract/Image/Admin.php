<?php
class Vpc_Abstract_Image_Admin extends Vpc_Abstract_Composite_Admin
{
    public function gridColumns()
    {
        $ret = array();
        $c = new Vps_Grid_Column('pic', trlVps('Image'), 100);
            $c->setData(new Vps_Data_Vpc_Image($this->_class, 'gridRow'))
            ->setRenderer('mouseoverPic');
        $ret['pic'] = $c;
        $c = new Vps_Grid_Column('pic_large');
            $c->setData(new Vps_Data_Vpc_Image($this->_class, 'gridRowLarge'));        
        $ret['pic_large'] = $c;

        if (Vpc_Abstract::getSetting($this->_class, 'imageCaption')) {
            $c = new Vps_Grid_Column('image_caption', trlVps('Image caption'));
            $c->setData(new Vps_Data_Vpc_Table(Vpc_Abstract::getSetting($this->_class, 'ownModel'), 'image_caption', $this->_class));
            $c->setEditor(new Vps_Form_Field_TextField());
            $ret['image_caption'] = $c;
        }
        return $ret;
    }
}
