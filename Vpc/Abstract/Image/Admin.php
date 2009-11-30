<?php
class Vpc_Abstract_Image_Admin extends Vpc_Abstract_Composite_Admin
{
    public function gridColumns()
    {
        $ret = array();
        $c = new Vps_Grid_Column('pic', trlVps('Image'), 100);
            $c->setData(new Vps_Data_Vpc_Image($this->_class, 'gridRow'))
            ->setRenderer('mouseoverPic');
        $ret[] = $c;
        $c = new Vps_Grid_Column('pic_large');
            $c->setData(new Vps_Data_Vpc_Image($this->_class, 'gridRowLarge'));        
        $ret[] = $c;
        return $ret;
    }
}