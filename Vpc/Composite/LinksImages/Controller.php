<?php
class Vpc_Composite_LinksImages_Controller extends Vpc_Abstract_List_Controller
{
    protected function _initColumns()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');
        $classes = Vpc_Abstract::getSetting($classes['child'], 'childComponentClasses');


        $this->_columns->add(new Vps_Auto_Grid_Column('image', trlVps('Image'), 40))
            ->setData(new Vpc_Composite_LinksImages_ImageData($classes['image'], $this->componentId, 'mini'))
            ->setRenderer('mouseoverPic');
        $this->_columns->add(new Vps_Auto_Grid_Column('pic_large'))
            ->setData(new Vpc_Composite_LinksImages_ImageData($classes['image'], $this->componentId, 'thumb'));

        parent::_initColumns();
    }
}
