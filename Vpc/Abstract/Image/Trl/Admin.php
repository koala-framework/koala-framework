<?php
class Vpc_Abstract_Image_Trl_Admin extends Vpc_Abstract_Composite_Trl_Admin
{
    public function gridColumns()
    {
        $ret = array();
        $c = new Vps_Grid_Column('pic', trlVps('Image'), 100);
            $c->setData(new Vpc_Abstract_Image_Trl_ImageData('gridRow'))
            ->setRenderer('mouseoverPic');
        $ret['pic'] = $c;
        $c = new Vps_Grid_Column('pic_large');
            $c->setData(new Vpc_Abstract_Image_Trl_ImageData('gridRowLarge'));
        $ret['pic_large'] = $c;
        return $ret;
    }
}
