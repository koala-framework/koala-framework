<?php
class Kwc_Basic_Text_Image_Controller extends Kwc_Abstract_Image_Controller
{
    protected $_formName = 'Kwc_Basic_Text_Image_Form';

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        //für rte
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($row->component_id)->getComponent();
        $this->view->imageUrl = $c->getImageUrl();
        $this->view->imageDimension = $c->getImageDimensions();
    }
}
