<?ph
class Vpc_Basic_Image_Controller extends Vps_Controller_Action_Auto_Vpc_For

    protected $_formName = 'Vpc_Basic_Image_Form'

    protected function _afterSave(Zend_Db_Table_Row_Abstract $row
    
        //für rt
        $this->view->imageUrl = $row->getImageUrl()
        $this->view->imageDimension = $row->getImageDimension()
    
