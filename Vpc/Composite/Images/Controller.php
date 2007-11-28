<?ph
class Vpc_Composite_Images_Controller extends Vps_Controller_Action_Auto_Vpc_Gri

    protected $_position = 'pos'

    protected function _initColumns(
    
        $this->_columns->add(new Vps_Auto_Grid_Column('filename', 'Filename', 200)
            ->setData(new Vps_Auto_Data_Vpc_Table('Vpc_Basic_Image_Model', 'filename'))
        $this->_columns->add(new Vps_Auto_Grid_Column('visible', 'Visible', 100)
            ->setEditor(new Vps_Auto_Field_Checkbox('visible', 'Visible'))
           
        $imageClass = Vpc_Abstract::getSetting($this->class, 'imageClass')
        $this->_columns->add(new Vps_Auto_Grid_Column('image', 'Image', 100)
            ->setData(new Vps_Auto_Data_Vpc_Image($imageClass, $this->pageId, $this->componentKey))
    

    protected function _beforeInsert($row
    
        $row->visible = 0
    
