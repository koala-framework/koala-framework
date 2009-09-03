<?php
class Vpc_ListSwitch_Controller extends Vpc_Abstract_List_Controller
{
    protected function _initColumns()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->_getParam('class'), 'child');

        $this->_columns->add(new Vps_Grid_Column('pic', trlVps('Image'), 100))
            ->setData(new Vps_Data_Vpc_Image($class, 'gridRow'))
            ->setRenderer('mouseoverPic');
        $this->_columns->add(new Vps_Grid_Column('pic_large'))
            ->setData(new Vps_Data_Vpc_Image($class, 'gridRowLarge'));
        parent::_initColumns();
    }
}
