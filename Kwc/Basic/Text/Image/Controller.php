<?php
class Vpc_Basic_Text_Image_Controller extends Vpc_Basic_Image_Controller
{
    protected $_formName = 'Vpc_Basic_Text_Image_Form';

    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
        //für rte
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($row->component_id)->getComponent();
        $this->view->imageUrl = $c->getImageUrl();
        $this->view->imageDimension = $c->getImageDimensions();
    }
}
