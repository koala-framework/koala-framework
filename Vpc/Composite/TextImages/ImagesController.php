<?php
class Vpc_Composite_TextImages_ImagesController extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_tableName = 'Vpc_Composite_TextImages_ImagesModel';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column('filename', 'Filename', 200))
            ->setData(new Vps_Auto_Data_Table_Vpc('Vpc_Basic_Image_IndexModel', 'filename'));
        $this->_columns->add(new Vps_Auto_Grid_Column('visible', 'Visible', 100))
            ->setEditor(new Vps_Auto_Field_Checkbox('visible', 'Visible'));
    }

    public function indexAction()
    {
        $config = array('controllerUrl' => $this->view->getControllerUrl($this->component, 'Vpc_Composite_TextImages_Images'));
        $this->view->ext('Vpc.Composite.TextImages.Index', $config);
    }
}