<?php
class Vpc_Basic_Text_Image_Controller extends Vpc_Basic_Image_Controller
{
    protected $_formName = 'Vpc_Basic_Text_Image_Form';

    protected function _afterSave(Vps_Model_Db_Row $row)
    {
        //für rte
        $this->view->imageUrl = $row->getRow()->getFileUrl();
        $this->view->imageDimension = $row->getRow()->getImageDimension();
    }
}
