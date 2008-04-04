<?php
class Vpc_Composite_Images_Controller extends Vpc_Abstract_List_Controller
{
    protected function _initColumns()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');

        if (Vpc_Abstract::getSetting($classes['child'], 'editComment')) {
            $data = new Vps_Auto_Data_Vpc_Table(
                'Vpc_Basic_Image_Model',
                'comment',
                'Vpc_Basic_Image_Component'
            );

            $this->_columns->add(new Vps_Auto_Grid_Column('comment', trlVps('Comment'), 195))
                ->setData($data);

        } else if (Vpc_Abstract::getSetting($classes['child'], 'editFilename')) {
            $data = new Vps_Auto_Data_Vpc_Table(
                'Vpc_Basic_Image_Model',
                'filename',
                'Vpc_Basic_Image_Component'
            );


            $this->_columns->add(new Vps_Auto_Grid_Column('filename', trlVps('Filename'), 195))
                ->setData($data);
        }


        $this->_columns->add(new Vps_Auto_Grid_Column($classes['child'], trlVps('Image'), 40))
            ->setData(new Vps_Auto_Data_Vpc_Image($classes['child'], 'mini'))
            ->setRenderer('mouseoverPic');
        $this->_columns->add(new Vps_Auto_Grid_Column('pic_large'))
            ->setData(new Vps_Auto_Data_Vpc_Image($classes['child'], 'thumb'));
        parent::_initColumns();
    }
}
