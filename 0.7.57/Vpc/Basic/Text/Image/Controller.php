<?php
class Vpc_Basic_Text_Image_Controller extends Vpc_Basic_Image_Controller
{
    protected $_formName = 'Vpc_Basic_Text_Image_Form';

    protected function _afterSave(Zend_Db_Table_Row_Abstract $row)
    {
        //für rte
        $this->view->imageUrl = $row->getFileUrl();
        $this->view->imageDimension = $row->getImageDimension();
    }
}
