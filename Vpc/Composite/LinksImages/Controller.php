<?php
class Vpc_Composite_LinksImages_Controller extends Vpc_Abstract_List_Controller
{
    protected function _initColumns()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->class, 'child');
        $classes = Vpc_Abstract::getChildComponentClasses($class, 'child');


        $this->_columns->add(new Vps_Grid_Column('image', trlVps('Image'), 100))
            ->setData(new Vpc_Composite_LinksImages_ImageData($classes['image'], 'gridRow'))
            ->setRenderer('mouseoverPic');
        $this->_columns->add(new Vps_Grid_Column('pic_large'))
            ->setData(new Vpc_Composite_LinksImages_ImageData($classes['image'], 'gridRowLarge'));

        parent::_initColumns();
    }
}
