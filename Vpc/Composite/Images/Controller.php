<?php
class Vpc_Composite_Images_Controller extends Vpc_Abstract_List_Controller
{
    protected function _initColumns()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->_getParam('class'), 'child');
/*
        if (Vpc_Abstract::getSetting($class, 'editComment')) {
            $data = new Vps_Data_Vpc_Table(
                'Vpc_Basic_Image_Model',
                'comment',
                'Vpc_Basic_Image_Component'
            );

            $this->_columns->add(new Vps_Grid_Column('comment', trlVps('Comment'), 195))
                ->setData($data);

        } else if (Vpc_Abstract::getSetting($class, 'editFilename')) {
            $data = new Vps_Data_Vpc_Table(
                'Vpc_Basic_Image_Model',
                'filename',
                'Vpc_Basic_Image_Component'
            );


            $this->_columns->add(new Vps_Grid_Column('filename', trlVps('Filename'), 195))
                ->setData($data);
        }
*/

        $this->_columns->add(new Vps_Grid_Column('pic', trlVps('Image'), 100))
            ->setData(new Vps_Data_Vpc_Image($class, 'gridRow'))
            ->setRenderer('mouseoverPic');
        $this->_columns->add(new Vps_Grid_Column('pic_large'))
            ->setData(new Vps_Data_Vpc_Image($class, 'gridRowLarge'));
        parent::_initColumns();
    }
}
